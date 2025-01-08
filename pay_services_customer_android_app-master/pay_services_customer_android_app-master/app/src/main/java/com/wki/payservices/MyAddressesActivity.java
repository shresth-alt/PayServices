package com.wki.payservices;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AlertDialog;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.Rect;
import android.os.Bundle;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ProgressBar;
import android.widget.Toast;
import com.android.volley.AuthFailureError;
import com.android.volley.DefaultRetryPolicy;
import com.android.volley.NetworkResponse;
import com.android.volley.Request;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.JsonObjectRequest;
import com.android.volley.toolbox.StringRequest;
import com.wki.payservices.adapter.AddressAdapter;
import com.wki.payservices.model.Address;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import io.github.inflationx.viewpump.ViewPumpContextWrapper;

public class MyAddressesActivity extends AppCompatActivity {

    private List<Address> addressList = new ArrayList<>();
    private RecyclerView listView;
    private Toolbar toolbar;
    ProgressBar progressBar;
    private SharedPreference sharedPreference = new SharedPreference();

    @Override
    protected void attachBaseContext(Context newBase) {
        super.attachBaseContext(ViewPumpContextWrapper.wrap(newBase));
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        getMenuInflater().inflate(R.menu.add_address_menu, menu);
        return super.onCreateOptionsMenu(menu);
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        int id = item.getItemId();
        if (id == R.id.action_add) {
            addNewAddress();
        } else if (id == android.R.id.home) {
            finish();
        }
        return super.onOptionsItemSelected(item);
    }

    EditText address, landmark, city, pincode;

    private void addNewAddress() {
        AlertDialog.Builder mBuilder = new AlertDialog.Builder(MyAddressesActivity.this);
        mBuilder.setTitle("Add New Address");
        final View customLayout = getLayoutInflater().inflate(R.layout.add_address_popup, null);
        address = customLayout.findViewById(R.id.ed_address);
        address.setText(_address);
        landmark = customLayout.findViewById(R.id.ed_landmark);
        landmark.setText(_landmark);
        city = customLayout.findViewById(R.id.ed_city);
        city.setText(_city);
        pincode = customLayout.findViewById(R.id.ed_pincode);
        pincode.setText(_pincode);
        mBuilder.setView(customLayout);
        mBuilder.setPositiveButton("Add New Address", new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface arg0, int arg1) {
                addCustomerAddress();
            }
        });
        mBuilder.setNegativeButton("Cancel", new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int which) {

            }
        });
        AlertDialog alertDialog = mBuilder.create();
        alertDialog.show();
    }

    String _address = "", _landmark = "", _city = "", _pincode = "";

    private void addCustomerAddress() {
        _address = address.getText().toString();
        _landmark = landmark.getText().toString();
        _city = city.getText().toString();
        _pincode = pincode.getText().toString();
        final String URL = Utils.DOMAIN + "/api/v1/user-addresses";
        HashMap<String, String> params = new HashMap<>();
        params.put("address", address.getText().toString());
        params.put("landmark", landmark.getText().toString());
        params.put("city", city.getText().toString());
        params.put("postal_code", pincode.getText().toString());
        Toast.makeText(this, "Adding new address...", Toast.LENGTH_SHORT).show();
        JsonObjectRequest req = new JsonObjectRequest(URL, new JSONObject(params), new Response.Listener<JSONObject>() {
            @Override
            public void onResponse(JSONObject response) {
                Toast.makeText(MyAddressesActivity.this, "New Address has been added!", Toast.LENGTH_SHORT).show();
                loadAddresses();
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
                        JSONArray errors = errorObject.getJSONArray("errors");
                        Toast.makeText(MyAddressesActivity.this, errors.getString(0), Toast.LENGTH_LONG).show();
                    } catch (JSONException e) {
                        Toast.makeText(MyAddressesActivity.this, e.getMessage(), Toast.LENGTH_SHORT).show();
                        e.printStackTrace();
                    }
                }
            }
        }){
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                HashMap<String, String> headers = new HashMap<>();
                headers.put("Authorization", "Bearer " + sharedPreference.getRememberToken(MyAddressesActivity.this));
                return headers;
            }
        };
        req.setRetryPolicy(new DefaultRetryPolicy(DefaultRetryPolicy.DEFAULT_TIMEOUT_MS * 1000, DefaultRetryPolicy.DEFAULT_MAX_RETRIES, DefaultRetryPolicy.DEFAULT_BACKOFF_MULT));
        ApplicationController.getInstance().addToRequestQueue(req);
    }


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_my_addresses);
        toolbar = findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);
        listView = findViewById(R.id.bookings_list);
        progressBar = findViewById(R.id.progressBar);
        if (getSupportActionBar() != null) {
            getSupportActionBar().setDisplayHomeAsUpEnabled(true);
            setTitle("My Addresses");
        }
        loadAddresses();
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
                    AddressAdapter addressAdapter = new AddressAdapter(addressList, MyAddressesActivity.this);
                    listView.setHasFixedSize(true);
                    listView.setLayoutManager(new GridLayoutManager(MyAddressesActivity.this, 1));
                    listView.addItemDecoration(new ItemOffsetDecoration(4));
                    listView.setAdapter(addressAdapter);
                    progressBar.setVisibility(View.GONE);
                } catch (JSONException e) {
                    Toast.makeText(MyAddressesActivity.this, e.getMessage(), Toast.LENGTH_SHORT).show();
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
                        if (response.statusCode == 401) {
                            /* Logout user */
                            sharedPreference.logoutUser(MyAddressesActivity.this);
                            Intent intent = new Intent(MyAddressesActivity.this, LoginActivity.class);
                            startActivity(intent);
                            finish();
                        } else {
                            JSONObject errorObject = new JSONObject(json);
                            JSONArray error_ = errorObject.getJSONArray("errors");
                            Toast.makeText(MyAddressesActivity.this, error_.get(0).toString(), Toast.LENGTH_LONG).show();
                        }
                    } catch (JSONException e) {
                        Toast.makeText(MyAddressesActivity.this, "Something went wrong on server", Toast.LENGTH_SHORT).show();
                        e.printStackTrace();
                    }
                }
            }
        }){
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                HashMap<String, String> headers = new HashMap<>();
                headers.put("Authorization", "Bearer " + sharedPreference.getRememberToken(MyAddressesActivity.this));
                headers.put("Accept", "application/json");
                return headers;
            }
        };
        strReq.setShouldCache(false);
        ApplicationController.getInstance().addToRequestQueue(strReq, Utils.VOLLEY_REQUESTS);
    }

    public static class ItemOffsetDecoration extends RecyclerView.ItemDecoration {
        private int offset;
        public ItemOffsetDecoration(int offset) {
            this.offset = offset;
        }
        @Override
        public void getItemOffsets(Rect outRect, View view, RecyclerView parent, RecyclerView.State state) {
            outRect.left = offset;
            outRect.right = offset;
            outRect.bottom = offset;
            outRect.top = offset;
        }
    }

}