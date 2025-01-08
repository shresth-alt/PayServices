package com.wki.payservices;

import androidx.appcompat.app.ActionBar;
import androidx.appcompat.app.AlertDialog;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;
import android.app.DatePickerDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.Bundle;
import android.view.Gravity;
import android.view.MenuItem;
import android.view.View;
import android.widget.Button;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.RadioGroup;
import android.widget.TextView;
import android.widget.Toast;
import com.android.volley.AuthFailureError;
import com.android.volley.DefaultRetryPolicy;
import com.android.volley.NetworkResponse;
import com.android.volley.Request;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.JsonObjectRequest;
import com.android.volley.toolbox.StringRequest;
import com.wki.payservices.model.Address;
import com.wki.payservices.model.Service;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import java.text.DateFormat;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.HashMap;
import java.util.List;
import java.util.Locale;
import java.util.Map;

import io.github.inflationx.viewpump.ViewPumpContextWrapper;

public class ServicesActivity extends AppCompatActivity {

    SharedPreference sharedPreference = new SharedPreference();

    private List<Service> serviceList = new ArrayList<>();
    private List<Service> serviceListLastLevel = new ArrayList<>();
    private String serviceID = "";
    private String serviceName = "";
    private String selectedDate = "", currentSelectedServiceID = "";
    private List<Address> addressList = new ArrayList<>();

    @Override
    protected void attachBaseContext(Context newBase) {
        super.attachBaseContext(ViewPumpContextWrapper.wrap(newBase));
    }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_services);
        Toolbar toolbar = findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);
        ActionBar actionBar = getSupportActionBar();
        Intent intent = getIntent();
        if (actionBar != null) {
            actionBar.setHomeButtonEnabled(true);
            actionBar.setDisplayHomeAsUpEnabled(true);
            actionBar.setTitle(intent.getStringExtra("service_name"));
        }
        Button btnBookService = findViewById(R.id.btn_book_service);
        btnBookService.setTransformationMethod(null);
        serviceID = intent.getStringExtra("service_id");
        serviceName = intent.getStringExtra("service_name");
        final RadioGroup locationRadio = findViewById(R.id.locationRadio);
        locationRadio.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener() {
            @Override
            public void onCheckedChanged(RadioGroup group, int checkedId) {
                if (checkedId == R.id.radioNewAddress) {
                    clearAddress();
                    if (!addressList.isEmpty()) showPreAddedAddresses();
                } else {
                    fillCurrentAddress();
                }
            }
        });
        loadServices();
        loadAddresses();
        setUpDatePicker();
        findViewById(R.id.btn_book_service).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if (locationRadio.getCheckedRadioButtonId() == -1) {
                    Toast.makeText(ServicesActivity.this, "Please select address type", Toast.LENGTH_SHORT).show();
                } else openConfirmationPopup();
            }
        });
    }

    int selectedDeliveryMethod = 0;

    private void showPreAddedAddresses() {
        final AlertDialog.Builder mBuilder = new AlertDialog.Builder(ServicesActivity.this);
        mBuilder.setTitle("Please choose address");
        String[] listItems = new String[addressList.size()];
        for (int i = 0; i < addressList.size(); i++) {
            listItems[i] = addressList.get(i).getAddress();
        }
        mBuilder.setSingleChoiceItems(listItems, selectedDeliveryMethod, new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialogInterface, int i) {
                selectedDeliveryMethod = i;
                fillSelectedAddress();
            }
        });
        mBuilder.setPositiveButton("Select", new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int which) {
                dialog.dismiss();
            }
        });
        mBuilder.setNegativeButton("I will enter new address", new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int which) {

            }
        });
        AlertDialog mDialog = mBuilder.create();
        mDialog.show();
    }

    private void loadAddresses() {
        StringRequest strReq = new StringRequest(Request.Method.GET, Utils.DOMAIN + "/api/v1/user-addresses", new Response.Listener<String>() {
            @Override
            public void onResponse(String response) {
                try {
                    addressList.clear();
                    JSONArray ordersArray = new JSONArray(response);
                    for (int i=0; i<ordersArray.length(); i++) {
                        JSONObject address = ordersArray.getJSONObject(i);
                        addressList.add(new Address(
                                address.getString("address"),
                                address.getString("landmark"),
                                address.getString("city"),
                                address.getString("postal_code"),
                                address.getString("id")
                        ));
                    }
                } catch (JSONException e) {
                    Toast.makeText(ServicesActivity.this, e.getMessage(), Toast.LENGTH_SHORT).show();
                    e.printStackTrace();
                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError error) {
                String json;
                NetworkResponse response = error.networkResponse;
                if (response != null && response.data != null) {
                    json = new String(response.data);
                    try {
                        JSONObject errorObject = new JSONObject(json);
                        JSONArray error_ = errorObject.getJSONArray("errors");
                        Toast.makeText(ServicesActivity.this, error_.get(0).toString(), Toast.LENGTH_LONG).show();
                    } catch (JSONException e) {
                        Toast.makeText(ServicesActivity.this, "Something went wrong on server", Toast.LENGTH_SHORT).show();
                        e.printStackTrace();
                    }
                }
            }
        }){
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                HashMap<String, String> headers = new HashMap<>();
                headers.put("Authorization", "Bearer " + sharedPreference.getRememberToken(ServicesActivity.this));
                return headers;
            }
        };
        strReq.setShouldCache(false);
        ApplicationController.getInstance().addToRequestQueue(strReq, Utils.VOLLEY_REQUESTS);
    }

    private void openConfirmationPopup() {
        AlertDialog.Builder alertDialogBuilder = new AlertDialog.Builder(ServicesActivity.this);
        alertDialogBuilder.setTitle("Confirm Booking");
        alertDialogBuilder.setMessage("100 Rs. will be the visiting charge if in any case, you deny the service after the visit. The final estimation of the amount paying will be given by the service person only. Do you want to book it?");
        alertDialogBuilder.setPositiveButton("Book Service", new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface arg0, int arg1) {
                createBooking();
            }
        });
        alertDialogBuilder.setNegativeButton("Cancel", new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int which) {

            }
        });
        AlertDialog alertDialog = alertDialogBuilder.create();
        alertDialog.show();
    }

    private void clearAddress() {
        ((EditText)findViewById(R.id.ed_address)).setText("");
        ((EditText)findViewById(R.id.ed_landmark)).setText("");
        ((EditText)findViewById(R.id.ed_city)).setText("");
        ((EditText)findViewById(R.id.ed_pincode)).setText("");
    }

    private void fillCurrentAddress() {
        ((EditText)findViewById(R.id.ed_address)).setText(SharedPreference.getPref(ServicesActivity.this, "address"));
        ((EditText)findViewById(R.id.ed_city)).setText(SharedPreference.getPref(ServicesActivity.this, "city"));
        ((EditText)findViewById(R.id.ed_pincode)).setText(SharedPreference.getPref(ServicesActivity.this, "postal_code"));
    }

    private void fillSelectedAddress() {
        ((EditText)findViewById(R.id.ed_address)).setText(addressList.get(selectedDeliveryMethod).getAddress());
        ((EditText)findViewById(R.id.ed_city)).setText(addressList.get(selectedDeliveryMethod).getCity());
        ((EditText)findViewById(R.id.ed_pincode)).setText(addressList.get(selectedDeliveryMethod).getPincode());
        ((EditText)findViewById(R.id.ed_landmark)).setText(addressList.get(selectedDeliveryMethod).getLandmark());
    }

    int[] dateWrappers = { R.id.today_date_wrapper, R.id.tomorrow_date_wrapper, R.id.next_date_wrapper, R.id.custom_date_wrapper };

    private void setUpDatePicker() {
        DateFormat dateFormat = new SimpleDateFormat("dd/MM", Locale.getDefault());
        DateFormat dayFormat = new SimpleDateFormat("EEEE", Locale.getDefault());
        DateFormat mysqlDate = new SimpleDateFormat("yyyy-MM-dd", Locale.getDefault());
        Calendar cal = Calendar.getInstance();
        for (int x = 0; x < dateWrappers.length - 1; x++) {
            String dataDate = dateFormat.format(cal.getTime());
            ((TextView)((LinearLayout)findViewById(dateWrappers[x])).getChildAt(0)).setText(dataDate);
            if (x == 2) {
                ((TextView)((LinearLayout)findViewById(dateWrappers[x])).getChildAt(1)).setText(dayFormat.format(cal.getTime()));
            }
            findViewById(dateWrappers[x]).setTag(mysqlDate.format(cal.getTime()));
            cal.add(Calendar.DATE, +1);
        }
        selectDateItem(findViewById(R.id.today_date_wrapper));
        for (final int dateWrapper : dateWrappers) {
            findViewById(dateWrapper).setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    unSelectAllDateItem();
                    selectDateItem(findViewById(dateWrapper));
                    if (dateWrapper == R.id.custom_date_wrapper) pickDate();
                }
            });
        }
    }

    private void selectDateItem(View viewById) {
        viewById.setSelected(true);
        if (viewById.getTag() != null) {
            selectedDate = viewById.getTag().toString();
        }
        ((TextView)((LinearLayout)viewById).getChildAt(0)).setTextColor(getResources().getColor(R.color.colorWhite));
        ((TextView)((LinearLayout)viewById).getChildAt(1)).setTextColor(getResources().getColor(R.color.colorWhite));
    }

    private void unSelectAllDateItem() {
        for (int dateWrapper : dateWrappers) {
            findViewById(dateWrapper).setSelected(false);
            ((TextView) ((LinearLayout) findViewById(dateWrapper)).getChildAt(0)).setTextColor(getResources().getColor(R.color.colorAccent));
            ((TextView) ((LinearLayout) findViewById(dateWrapper)).getChildAt(1)).setTextColor(getResources().getColor(R.color.colorAccent));
        }
    }

    private void loadServices() {
        StringRequest strReq = new StringRequest(Request.Method.GET, Utils.DOMAIN + "/api/v1/services?parent_id=" + serviceID, new Response.Listener<String>() {
            @Override
            public void onResponse(String response) {
                try {
                    findViewById(R.id.progressBar).setVisibility(View.GONE);
                    JSONObject object = new JSONObject(response);
                    JSONArray servicesArray = object.getJSONArray("data");
                    for (int i=0; i<servicesArray.length(); i++) {
                        JSONObject service = servicesArray.getJSONObject(i);
                        serviceList.add(new Service(
                                service.getString("id"),
                                service.getString("name"),
                                ""
                        ));
                    }
                    createServicesScroller();
                } catch (JSONException e) {
                    Toast.makeText(ServicesActivity.this, e.getMessage(), Toast.LENGTH_SHORT).show();
                    e.printStackTrace();
                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError error) {
                String json;
                NetworkResponse response = error.networkResponse;
                if (response != null && response.data != null) {
                    json = new String(response.data);
                    try {
                        JSONObject errorObject = new JSONObject(json);
                        JSONArray error_ = errorObject.getJSONArray("errors");
                        Toast.makeText(ServicesActivity.this, error_.get(0).toString(), Toast.LENGTH_LONG).show();
                    } catch (JSONException e) {
                        Toast.makeText(ServicesActivity.this, "Something went wrong on server", Toast.LENGTH_SHORT).show();
                        e.printStackTrace();
                    }
                }
            }
        }){
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                HashMap<String, String> headers = new HashMap<>();
                headers.put("Authorization", "Bearer " + sharedPreference.getRememberToken(ServicesActivity.this));
                return headers;
            }
        };;
        strReq.setShouldCache(false);
        ApplicationController.getInstance().addToRequestQueue(strReq, Utils.VOLLEY_REQUESTS);
    }

    private void loadLastLevelServices() {
        StringRequest strReq = new StringRequest(Request.Method.GET, Utils.DOMAIN + "/api/v1/services?parent_id=" + currentSelectedServiceID, new Response.Listener<String>() {
            @Override
            public void onResponse(String response) {
                try {
                    findViewById(R.id.progressBar).setVisibility(View.GONE);
                    JSONObject object = new JSONObject(response);
                    JSONArray servicesArray = object.getJSONArray("data");
                    serviceListLastLevel.clear();
                    for (int i=0; i<servicesArray.length(); i++) {
                        JSONObject service = servicesArray.getJSONObject(i);
                        serviceListLastLevel.add(new Service(
                                service.getString("id"),
                                service.getString("name"),
                                "",
                                service.getString("charges")
                        ));
                    }
                    if (!serviceListLastLevel.isEmpty()) openSubServicePopup();
                } catch (JSONException e) {
                    Toast.makeText(ServicesActivity.this, e.getMessage(), Toast.LENGTH_SHORT).show();
                    e.printStackTrace();
                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError error) {
                String json;
                NetworkResponse response = error.networkResponse;
                if (response != null && response.data != null) {
                    json = new String(response.data);
                    try {
                        JSONObject errorObject = new JSONObject(json);
                        JSONArray error_ = errorObject.getJSONArray("errors");
                        Toast.makeText(ServicesActivity.this, error_.get(0).toString(), Toast.LENGTH_LONG).show();
                    } catch (JSONException e) {
                        Toast.makeText(ServicesActivity.this, "Something went wrong on server", Toast.LENGTH_SHORT).show();
                        e.printStackTrace();
                    }
                }
            }
        }){
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                HashMap<String, String> headers = new HashMap<>();
                headers.put("Authorization", "Bearer " + sharedPreference.getRememberToken(ServicesActivity.this));
                return headers;
            }
        };;
        strReq.setShouldCache(false);
        ApplicationController.getInstance().addToRequestQueue(strReq, Utils.VOLLEY_REQUESTS);
    }

    private void createServicesScroller() {
        if (serviceList.size() == 0) {
            serviceList.add(new Service(serviceID, serviceName, ""));
        }
        final LinearLayout servicesScrollView = findViewById(R.id.services_scroll_view);
        for (int i=0; i<serviceList.size(); i++) {
            final View listItem = getLayoutInflater().inflate(R.layout.sc_service_item, servicesScrollView, false);
            ((TextView)listItem.findViewById(R.id.service_name)).setText(serviceList.get(i).getName());
            (listItem.findViewById(R.id.service_name)).setTag(serviceList.get(i).getId());
            listItem.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    unSelectItems(servicesScrollView);
                    setItemSelected(listItem);
                }
            });
            servicesScrollView.addView(listItem);
        }
    }

    private void unSelectItems(LinearLayout servicesScrollView) {
        for (int i = 0; i < servicesScrollView.getChildCount(); i++) {
            View view = servicesScrollView.getChildAt(i);
            if (view instanceof TextView) {
                setItemUnSelected(view);
            }
        }
    }

    private void setItemSelected(View listItem) {
        listItem.setSelected(true);
        ((TextView)listItem.findViewById(R.id.service_name)).setTextColor(getResources().getColor(R.color.colorWhite));
        ((TextView)findViewById(R.id.selected_service)).setText(((TextView)listItem.findViewById(R.id.service_name)).getText().toString());
        currentSelectedServiceID = listItem.findViewById(R.id.service_name).getTag().toString();
        findViewById(R.id.selected_service).setVisibility(View.VISIBLE);
        findViewById(R.id.service_line).setVisibility(View.VISIBLE);
        loadLastLevelServices();
    }

    private void openSubServicePopup() {
        /* Try to call sub services */
        final AlertDialog.Builder mBuilder = new AlertDialog.Builder(ServicesActivity.this);
        mBuilder.setTitle("Please choose service");
        String[] listItems = new String[serviceListLastLevel.size()];
        for (int i = 0; i < serviceListLastLevel.size(); i++) {
            listItems[i] = serviceListLastLevel.get(i).getName() + " [" + getResources().getString(R.string.label_rs_sym) + serviceListLastLevel.get(i).getCharges() + "]";
        }
        mBuilder.setSingleChoiceItems(listItems, Integer.parseInt(currentSelectedServiceID), new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialogInterface, int i) {
                currentSelectedServiceID = serviceListLastLevel.get(i).getId();
                ((TextView)findViewById(R.id.selected_service)).setText(serviceListLastLevel.get(i).getName());
                findViewById(R.id.selected_service).setVisibility(View.VISIBLE);
                findViewById(R.id.service_line).setVisibility(View.VISIBLE);
            }
        });
        mBuilder.setPositiveButton("Select", new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int which) {
                dialog.dismiss();
            }
        });
        mBuilder.setNegativeButton("Cancel", new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int which) {

            }
        });
        AlertDialog mDialog = mBuilder.create();
        mDialog.show();
    }

    private void setItemUnSelected(View listItem) {
        listItem.setSelected(false);
        ((TextView)listItem.findViewById(R.id.service_name)).setTextColor(getResources().getColor(R.color.colorAccent));
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        if (item.getItemId() == android.R.id.home) {
            this.finish();
            return true;
        }
        return super.onOptionsItemSelected(item);
    }

    private void pickDate() {
        final Calendar calendar = Calendar.getInstance();
        int day = calendar.get(Calendar.DAY_OF_MONTH);
        int month = calendar.get(Calendar.MONTH);
        int year = calendar.get(Calendar.YEAR);
        DatePickerDialog datePickerDialog = new DatePickerDialog(ServicesActivity.this,
                new DatePickerDialog.OnDateSetListener() {
                    @Override
                    public void onDateSet(DatePicker view, int year, int monthOfYear, int dayOfMonth) {
                        String moy = (monthOfYear + 1) < 10 ? ("0" + (monthOfYear + 1)) : String.valueOf(monthOfYear + 1);
                        (((LinearLayout) findViewById(R.id.custom_date_wrapper)).getChildAt(0)).setVisibility(View.VISIBLE);
                        String dateFormat = (dayOfMonth < 10 ? "0" + dayOfMonth : dayOfMonth) + "/" + moy;
                        selectedDate = year + "-" + moy + "-" +  (dayOfMonth < 10 ? "0" + dayOfMonth : dayOfMonth);
                        ((TextView) ((LinearLayout) findViewById(R.id.custom_date_wrapper)).getChildAt(0)).setText(dateFormat);
                        try {
                            Date date = null;
                            String dateString = String.format(Locale.getDefault(), "%d-%d-%d", year, monthOfYear + 1, dayOfMonth);
                            date = new SimpleDateFormat("yyyy-M-d", Locale.getDefault()).parse(dateString);
                            if (date != null) {
                                String dayOfWeek = new SimpleDateFormat("EEEE", Locale.ENGLISH).format(date);
                                ((TextView) ((LinearLayout) findViewById(R.id.custom_date_wrapper)).getChildAt(1)).setText(dayOfWeek);
                            }
                        } catch (ParseException e) {
                            e.printStackTrace();
                        }

                    }
                }, year, month, day);
        datePickerDialog.getDatePicker().setMinDate(System.currentTimeMillis() - 1000);
        datePickerDialog.show();
        datePickerDialog.setTitle("Select Date");
    }

    private void createBooking() {
        ((Button)findViewById(R.id.btn_book_service)).setText(R.string.label_booking_service);
        findViewById(R.id.btn_book_service).setEnabled(false);
        final String URL = Utils.DOMAIN + "/api/v1/orders";
        HashMap<String, String> params = new HashMap<>();
        params.put("service_id", currentSelectedServiceID);
        params.put("address", ((EditText)findViewById(R.id.ed_address)).getText().toString());
        params.put("landmark", ((EditText)findViewById(R.id.ed_landmark)).getText().toString());
        params.put("city", ((EditText)findViewById(R.id.ed_city)).getText().toString());
        params.put("postal_code", ((EditText)findViewById(R.id.ed_pincode)).getText().toString());
        params.put("service_date", selectedDate);
        JsonObjectRequest req = new JsonObjectRequest(URL, new JSONObject(params), new Response.Listener<JSONObject>() {
            @Override
            public void onResponse(JSONObject response) {
                ((Button)findViewById(R.id.btn_book_service)).setText(R.string.label_book_service);
                findViewById(R.id.btn_book_service).setEnabled(true);
                Toast.makeText(ServicesActivity.this, "We have received your booking! One of our executives will call you soon!", Toast.LENGTH_SHORT).show();
                finish();
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError error) {
                ((Button)findViewById(R.id.btn_book_service)).setText(R.string.label_book_service);
                findViewById(R.id.btn_book_service).setEnabled(true);
                String json;
                NetworkResponse response = error.networkResponse;
                if (response != null && response.data != null) {
                    json = new String(response.data);
                    try {
                        JSONObject errorObject = new JSONObject(json);
                        JSONArray errors = errorObject.getJSONArray("errors");
                        Toast toast = Toast.makeText(ServicesActivity.this, errors.getString(0), Toast.LENGTH_LONG);
                        toast.setGravity(Gravity.CENTER, 0, 0);
                        toast.show();
                    } catch (JSONException e) {
                        Toast.makeText(ServicesActivity.this, e.getMessage(), Toast.LENGTH_SHORT).show();
                        e.printStackTrace();
                    }
                }
            }
        }){
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                HashMap<String, String> headers = new HashMap<>();
                headers.put("Authorization", "Bearer " + sharedPreference.getRememberToken(ServicesActivity.this));
                return headers;
            }
        };
        req.setRetryPolicy(new DefaultRetryPolicy(DefaultRetryPolicy.DEFAULT_TIMEOUT_MS * 1000, DefaultRetryPolicy.DEFAULT_MAX_RETRIES, DefaultRetryPolicy.DEFAULT_BACKOFF_MULT));
        ApplicationController.getInstance().addToRequestQueue(req);
    }
}