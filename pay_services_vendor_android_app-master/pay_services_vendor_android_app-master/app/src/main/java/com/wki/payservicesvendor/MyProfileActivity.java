package com.wki.payservicesvendor;

import androidx.annotation.NonNull;
import androidx.annotation.RequiresApi;
import androidx.appcompat.app.AlertDialog;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;
import androidx.core.app.ActivityCompat;
import androidx.core.content.ContextCompat;
import android.Manifest;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.database.Cursor;
import android.graphics.Bitmap;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.provider.MediaStore;
import android.text.TextUtils;
import android.util.Base64;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.ScrollView;
import android.widget.TextView;
import android.widget.Toast;
import com.android.volley.AuthFailureError;
import com.android.volley.DefaultRetryPolicy;
import com.android.volley.NetworkResponse;
import com.android.volley.Request;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.StringRequest;
import com.bumptech.glide.Glide;
import com.google.gson.JsonObject;
import com.razorpay.Checkout;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.nio.charset.StandardCharsets;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import com.razorpay.PaymentData;
import com.razorpay.PaymentResultWithDataListener;
import io.github.inflationx.viewpump.ViewPumpContextWrapper;

public class MyProfileActivity extends AppCompatActivity implements View.OnClickListener, PaymentResultWithDataListener {

    private Toolbar toolbar;
    ImageView ivAadharFront, ivAadharBack, ivDL, ivPan, ivPhoto, ivCheque, ivSignature, ivInsuranceDoc;
    EditText etName, etFatherName, etDOB, etEmail, etMobile, etAltMobile, etCurrAddress, etPermanentAddress, etVillage, etTown;
    EditText etCity, etDistrict, etState, etPinCode, etOtherQualification;
    RadioGroup rgQualification;
    String imgAadharFront, imgAadharBack, imgDL, imgPanCard, imgPhoto, imgCheque, imgSignature, imgInsuranceDoc;
    private static final int AADHAR_IMAGE_FIRST_REQUEST = 1;
    private static final int AADHAR_IMAGE_BACK_REQUEST = 8;
    private static final int DL_IMAGE_REQUEST = 2;
    private static final int PAN_IMAGE_REQUEST = 3;
    private static final int PHOTO_IMAGE_REQUEST = 4;
    private static final int CHEQUE_IMAGE_REQUEST = 5;
    private static final int SIGNATURE_IMAGE_REQUEST = 6;
    private static final int INSURANCE_IMAGE_REQUEST = 7;
    private static final int STORAGE_PERMISSION_REQUEST = 9;
    private SharedPreference sharedPreference = new SharedPreference();
    ProgressBar progressBar;
    Button btnEntryFee, btnWalletBalance, btnSelectService;
    Checkout checkoutPay;
    String currentPaymentType = "";
    int payment = 0;
    List<Integer> myServices, selectedServices;
    HashMap<Integer, String> allServices;
    String vendorServicesIds = "";

    @Override
    protected void attachBaseContext(Context newBase) {
        super.attachBaseContext(ViewPumpContextWrapper.wrap(newBase));
    }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_my_profile);
        toolbar = findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);
        myServices = new ArrayList<>();
        if (getSupportActionBar() != null) {
            getSupportActionBar().setDisplayHomeAsUpEnabled(true);
            getSupportActionBar().setTitle("My Profile");
        }
        progressBar = findViewById(R.id.progressBar);
        ivAadharFront = findViewById(R.id.aadhar_front);
        ivAadharBack = findViewById(R.id.aadhar_back);
        ivDL = findViewById(R.id.driving_license);
        ivPan = findViewById(R.id.pan_card);
        ivPhoto = findViewById(R.id.photo);
        ivCheque = findViewById(R.id.cancel_cheque);
        ivSignature = findViewById(R.id.signature);
        ivInsuranceDoc = findViewById(R.id.insurance_doc);
        etOtherQualification = findViewById(R.id.ed_other_qualification);
        rgQualification = findViewById(R.id.radio_group_qualification);
        btnEntryFee = findViewById(R.id.btn_pay_entry_fee);
        btnEntryFee.setTransformationMethod(null);
        btnWalletBalance = findViewById(R.id.btn_wallet_update);
        btnWalletBalance.setTransformationMethod(null);
        btnSelectService = findViewById(R.id.btn_select_service);
        btnSelectService.setTransformationMethod(null);

        etName = findViewById(R.id.ed_name);
        etFatherName = findViewById(R.id.ed_father_name);
        etDOB = findViewById(R.id.ed_dob);
        etEmail = findViewById(R.id.ed_email);
        etMobile = findViewById(R.id.ed_mobile);
        etAltMobile = findViewById(R.id.ed_alternate_mobile);
        etCurrAddress = findViewById(R.id.ed_address);
        etPermanentAddress = findViewById(R.id.ed_p_address);
        etVillage = findViewById(R.id.ed_village);
        etTown = findViewById(R.id.ed_tehsil_town);
        etCity = findViewById(R.id.ed_city);
        etDistrict = findViewById(R.id.ed_district);
        etState = findViewById(R.id.ed_state);
        etPinCode = findViewById(R.id.ed_pin_code);

        ivAadharFront.setOnClickListener(this);
        ivAadharBack.setOnClickListener(this);
        ivDL.setOnClickListener(this);
        ivPan.setOnClickListener(this);
        ivPhoto.setOnClickListener(this);
        ivCheque.setOnClickListener(this);
        ivSignature.setOnClickListener(this);
        ivInsuranceDoc.setOnClickListener(this);
        btnEntryFee.setOnClickListener(this);
        btnWalletBalance.setOnClickListener(this);
        btnSelectService.setOnClickListener(this);

        rgQualification.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener() {
            @Override
            public void onCheckedChanged(RadioGroup group, int checkedId) {
                if (checkedId == R.id.other) {
                    etOtherQualification.setVisibility(View.VISIBLE);
                } else etOtherQualification.setVisibility(View.GONE);
            }
        });
        if (!hasPermission()) {
            askPermission();
        }
        checkoutPay = new Checkout();
        checkoutPay.setKeyID(Utils.RAZORPAY_KEY);
        Checkout.preload(this);
        setProfileData();
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        getMenuInflater().inflate(R.menu.my_profile, menu);
        return true;
    }

    private void setProfileData() {
        if (Utils.isConnected(this)) {
            progressBar.setVisibility(View.VISIBLE);
            StringRequest strReq = new StringRequest(Request.Method.GET, Utils.DOMAIN + "/api/v1/current-user", new Response.Listener<String>() {
                @Override
                public void onResponse(String response) {
                    try {
                        progressBar.setVisibility(View.GONE);
                        JSONObject object = new JSONObject(response);

                        etName.setText(object.getString("name"));
                        etMobile.setText(object.getString("mobile"));
                        etEmail.setText(object.getString("email"));
                        ((TextView)findViewById(R.id.value_wallet_balance)).setText(object.getString("wallet_balance"));
                        etName.setEnabled(false);
                        etMobile.setEnabled(false);
                        if (object.getDouble("membership_fee") != 0) {
                            btnEntryFee.setVisibility(View.GONE);
                        }
                        if (object.getInt("is_verified") == 1) {
                            findViewById(R.id.documentation_wrapper).setVisibility(View.GONE);
                            btnSelectService.setVisibility(View.GONE);
                        }
                        if (object.has("detail") && !object.isNull("detail")) {
                            JSONObject userDetail = object.getJSONObject("detail");
                            etFatherName.setText(userDetail.getString("father_name"));
                            etDOB.setText(userDetail.getString("dob"));
                            etAltMobile.setText(userDetail.getString("alt_mobile"));
                            etCurrAddress.setText(userDetail.getString("cur_address"));
                            etPermanentAddress.setText(userDetail.getString("per_address"));
                            etVillage.setText(userDetail.getString("village"));
                            etTown.setText(userDetail.getString("town"));
                            etCity.setText(userDetail.getString("city"));
                            etDistrict.setText(userDetail.getString("district"));
                            if (userDetail.has("state") && !userDetail.isNull("state")) {
                                etState.setText(userDetail.getString("state"));
                            }
                            etPinCode.setText(userDetail.getString("pincode"));
                            String qualification = userDetail.getString("qualification");
                            if (qualification.equals(getString(R.string.label_tenth))) {
                                rgQualification.check(R.id.tenth);
                            } else if (qualification.equals(getString(R.string.label_twelve))) {
                                rgQualification.check(R.id.twelve);
                            } else if (qualification.equals(getString(R.string.label_diploma))) {
                                rgQualification.check(R.id.diploma);
                            } else {
                                rgQualification.check(R.id.other);
                                etOtherQualification.setText(qualification);
                            }
                            String aadharFrontURI = Utils.MEDIA_DOMAIN + userDetail.getString("aadhar_front");
                            String aadharBackURI = Utils.MEDIA_DOMAIN + userDetail.getString("aadhar_back");
                            String dlURI = Utils.MEDIA_DOMAIN + userDetail.getString("driving_license");
                            String panCardURI = Utils.MEDIA_DOMAIN + userDetail.getString("pan_card");
                            String photoURI = Utils.MEDIA_DOMAIN + userDetail.getString("photo");
                            String chequeURI = Utils.MEDIA_DOMAIN + userDetail.getString("cheque");
                            String signatureURI = Utils.MEDIA_DOMAIN + userDetail.getString("signature");
                            String insuranceURI = Utils.MEDIA_DOMAIN + userDetail.getString("insurance");
                            Glide.with(MyProfileActivity.this).load(aadharFrontURI).into(ivAadharFront);
                            Glide.with(MyProfileActivity.this).load(aadharBackURI).into(ivAadharBack);
                            Glide.with(MyProfileActivity.this).load(dlURI).into(ivDL);
                            Glide.with(MyProfileActivity.this).load(panCardURI).into(ivPan);
                            Glide.with(MyProfileActivity.this).load(photoURI).into(ivPhoto);
                            Glide.with(MyProfileActivity.this).load(chequeURI).into(ivCheque);
                            Glide.with(MyProfileActivity.this).load(signatureURI).into(ivSignature);
                            Glide.with(MyProfileActivity.this).load(insuranceURI).into(ivInsuranceDoc);
                        }
                        if (object.has("services") && !object.isNull("services")) {
                            JSONArray servicesArr = object.getJSONArray("services");
                            if (servicesArr.length() > 0) {
                                for (int i = 0; i < servicesArr.length(); i++) {
                                    myServices.add(servicesArr.getJSONObject(i).getInt("service_id"));
                                }
                            }
                        }

                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                }
            }, new Response.ErrorListener() {
                @RequiresApi(api = Build.VERSION_CODES.KITKAT)
                @Override
                public void onErrorResponse(VolleyError error) {
                    progressBar.setVisibility(View.GONE);
                    NetworkResponse networkResponse = error.networkResponse;
                    if (networkResponse != null) {
                        int errorCode = networkResponse.statusCode;
                        String responseBody = new String(error.networkResponse.data, StandardCharsets.UTF_8);
                        Log.d("PROFILE_ERR_ERR_", ""+responseBody);
                    }
                }
            }) {
                @Override
                public Map<String, String> getHeaders() {
                    HashMap<String, String> headers = new HashMap<>();
                    headers.put("Authorization", "Bearer " + sharedPreference.getRememberToken(MyProfileActivity.this));
                    return headers;
                }
            };
            ApplicationController.getInstance().addToRequestQueue(strReq, Utils.VOLLEY_REQUESTS);

        } else {
            Toast.makeText(this, R.string.connection_error, Toast.LENGTH_SHORT).show();
        }
    }

    @Override
    public boolean onOptionsItemSelected(@NonNull MenuItem item) {
        int id = item.getItemId();
        if (id == android.R.id.home) {
            finish();
        } else if (id == R.id.action_submit) {
            if (!hasPermission()) {
                Toast.makeText(this, R.string.error_storage_permission, Toast.LENGTH_SHORT).show();
                askPermission();
            } else {
                submitData(true);
            }
        }
        return super.onOptionsItemSelected(item);
    }

    @Override
    public void onClick(View v) {
        Intent intent = new Intent();
        intent.setType("image/*");
        intent.setAction(Intent.ACTION_GET_CONTENT);
        switch (v.getId()) {
            case R.id.aadhar_front:
                chooseImage(AADHAR_IMAGE_FIRST_REQUEST);
                break;
            case R.id.aadhar_back:
                chooseImage(AADHAR_IMAGE_BACK_REQUEST);
                break;
            case R.id.driving_license:
                chooseImage(DL_IMAGE_REQUEST);
                break;
            case R.id.pan_card:
                chooseImage(PAN_IMAGE_REQUEST);
                break;
            case R.id.photo:
                chooseImage(PHOTO_IMAGE_REQUEST);
                break;
            case R.id.cancel_cheque:
                chooseImage(CHEQUE_IMAGE_REQUEST);
                break;
            case R.id.signature:
                chooseImage(SIGNATURE_IMAGE_REQUEST);
                break;
            case R.id.insurance_doc:
                chooseImage(INSURANCE_IMAGE_REQUEST);
                break;
            case R.id.btn_pay_entry_fee:
                initiatePayment();
                break;
            case R.id.btn_wallet_update:
                addPaymentToWallet();
                break;
            case R.id.btn_select_service:
                getAllServices();
                break;
        }
    }

    private void selectService() {
        AlertDialog.Builder builder = new AlertDialog.Builder(this);
        builder.setTitle(R.string.select_service);
        builder.setCancelable(false);
        ScrollView scrollView = new ScrollView(this);
        final LinearLayout linearLayout = new LinearLayout(this);
        linearLayout.setOrientation(LinearLayout.VERTICAL);
        linearLayout.setPadding(10,10,10,0);
        if (allServices != null && allServices.size() > 0) {
            for (Map.Entry<Integer, String> entry : allServices.entrySet()) {
                CheckBox checkBox = new CheckBox(this);
                checkBox.setId(entry.getKey());
                checkBox.setText(entry.getValue());
                if (myServices != null && myServices.contains(entry.getKey())) {
                    checkBox.setChecked(true);
                }
                linearLayout.addView(checkBox);
            }
        }
        scrollView.addView(linearLayout);
        builder.setView(scrollView);
        builder.setPositiveButton("Choose Services", new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int which) {
                selectedServices = new ArrayList<>();
                for (int i = 0; i < linearLayout.getChildCount(); i++) {
                    CheckBox cb = ((CheckBox) linearLayout.getChildAt(i));
                    if (cb.isChecked()) {
                        selectedServices.add(cb.getId());
                        if (!myServices.contains(cb.getId())) myServices.add(cb.getId());
                    }
                }
                vendorServicesIds = TextUtils.join(",", selectedServices);
            }
        });
        builder.setNegativeButton("Cancel", null);
        AlertDialog alertDialog = builder.create();
        alertDialog.show();
        alertDialog.getButton(DialogInterface.BUTTON_POSITIVE).setAllCaps(false);
        alertDialog.getButton(DialogInterface.BUTTON_NEGATIVE).setAllCaps(false);
        alertDialog.getButton(DialogInterface.BUTTON_POSITIVE).setTextSize(16);
        alertDialog.getButton(DialogInterface.BUTTON_NEGATIVE).setTextSize(16);
    }

    private void getAllServices() {
        if (Utils.isConnected(this)) {
            Utils.showLoader(this, null);
            StringRequest strReq = new StringRequest(
                    Request.Method.GET,
                    Utils.DOMAIN + "/api/v1/vendor-services",
                    new Response.Listener<String>() {
                @Override
                public void onResponse(String response) {
                    try {
                        JSONArray data = new JSONArray(response);
                        if (data.length() > 0) {
                            allServices = new HashMap<>();
                            for (int i = 0; i < data.length(); i++) {
                                int id = data.getJSONObject(i).getInt("id");
                                String name = data.getJSONObject(i).getString("service_name");
                                allServices.put(id, name);
                            }
                            selectService();
                        }
                    } catch (JSONException e) {
                        e.printStackTrace();
                        Toast.makeText(MyProfileActivity.this, R.string.something_went_wrong, Toast.LENGTH_SHORT).show();
                    }

                    Utils.hideLoader(MyProfileActivity.this);
                }

            }, new Response.ErrorListener() {
                @RequiresApi(api = Build.VERSION_CODES.KITKAT)
                @Override
                public void onErrorResponse(VolleyError error) {
                    Utils.hideLoader(MyProfileActivity.this);

                    Toast.makeText(MyProfileActivity.this, R.string.something_went_wrong, Toast.LENGTH_SHORT).show();

                    NetworkResponse networkResponse = error.networkResponse;
                    if (networkResponse != null) {
                        int errorCode = networkResponse.statusCode;
                        String responseBody = new String(error.networkResponse.data, StandardCharsets.UTF_8);
                        Log.d("VENDOR_SERVICES_ERR_", ""+responseBody);
                    }
                }
            }) {
                @Override
                public Map<String, String> getHeaders() {
                    HashMap<String, String> headers = new HashMap<>();
                    headers.put("Authorization", "Bearer " + sharedPreference.getRememberToken(MyProfileActivity.this));
                    return headers;
                }
            };
            ApplicationController.getInstance().addToRequestQueue(strReq, Utils.VOLLEY_REQUESTS);

        } else {
            Toast.makeText(this, R.string.connection_error, Toast.LENGTH_SHORT).show();
        }
    }

    private void chooseImage(final int requestCode) {
        if (hasPermission()) {
            final String[] options = getResources().getStringArray(R.array.image_chooser);
            AlertDialog.Builder alertDialog = new AlertDialog.Builder(MyProfileActivity.this);
            alertDialog.setItems(options, new DialogInterface.OnClickListener() {
                @Override
                public void onClick(DialogInterface dialog, int checkedItem) {
                    if (checkedItem == 0) {
                        Intent takePictureIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
                        startActivityForResult(takePictureIntent, requestCode);
                    } else {
                        Intent intent = new Intent();
                        intent.setType("image/*");
                        intent.setAction(Intent.ACTION_GET_CONTENT);
                        startActivityForResult(Intent.createChooser(intent, "Select Picture"), requestCode);
                    }
                }
            });
            alertDialog.show();

        } else askPermission();
    }

    private void initiatePayment() {
        currentPaymentType = "Membership Fee";
        payment = 500;
        createOrderOnRazorpay();
    }

    private void addPaymentToWallet() {
        /* Show alert here */
        AlertDialog.Builder mBuilder = new AlertDialog.Builder(MyProfileActivity.this);
        mBuilder.setTitle("Wallet Amount Update");
        final View customLayout = getLayoutInflater().inflate(R.layout.wallet_amount_popup, null);
        final EditText amount = customLayout.findViewById(R.id.wallet_amount);
        mBuilder.setView(customLayout);
        mBuilder.setPositiveButton("Continue", new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface arg0, int arg1) {
                if (!amount.getText().toString().trim().isEmpty()) {
                    initiatePaymentWalletUpdate(amount.getText().toString());
                } else {
                    Toast.makeText(MyProfileActivity.this, "Please enter a valid amount", Toast.LENGTH_SHORT).show();
                }
            }
        });
        mBuilder.setNegativeButton("Cancel", null);
        AlertDialog alertDialog = mBuilder.create();
        alertDialog.show();
    }

    private void initiatePaymentWalletUpdate(String amount) {
        payment = Integer.parseInt(amount);
        currentPaymentType = "Wallet Amount";
        createOrderOnRazorpay();
    }

    private void createOrderOnRazorpay() {
        String url = "https://api.razorpay.com/v1/orders";
        StringRequest strReq = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String response) {
                try {
                    JSONObject data = new JSONObject(response);
                    String razorpayOrderID = data.getString("id");
                    JSONObject options = new JSONObject();
                    options.put("name", "Pay Services");
                    options.put("description", currentPaymentType);
                    options.put("image", Utils.DOMAIN + "/pay-logo.png");
                    options.put("order_id", razorpayOrderID);
                    options.put("theme.color", "#1474a4");
                    options.put("currency", "INR");
                    options.put("amount", String.valueOf(payment * 100));
                    JSONObject preFill = new JSONObject();
                    preFill.put("email", "support@payservice.in");
                    preFill.put("contact", etMobile.getText().toString());
                    options.put("prefill", preFill);
                    checkoutPay.open(MyProfileActivity.this, options);
                } catch (JSONException e) {
                    Toast.makeText(MyProfileActivity.this, "i came here", Toast.LENGTH_SHORT).show();
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
                        Toast.makeText(MyProfileActivity.this, error_.get(0).toString(), Toast.LENGTH_LONG).show();
                    } catch (JSONException e) {
                        Toast.makeText(MyProfileActivity.this, "Something went wrong on server", Toast.LENGTH_SHORT).show();
                        e.printStackTrace();
                    }
                }
            }
        }) {
            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<>();
                params.put("amount", String.valueOf(payment * 100));
                params.put("currency", "INR");
                return params;
            }
            @Override
            public Map<String, String> getHeaders() {
                HashMap<String, String> headers = new HashMap<>();
                String encodedCredentials = Base64.encodeToString((Utils.RAZORPAY_KEY + ":" + Utils.RAZORPAY_SECRET).getBytes(), Base64.NO_WRAP);
                headers.put("Authorization", "Basic " + encodedCredentials);
                return headers;
            }
        };
        ApplicationController.getInstance().addToRequestQueue(strReq, Utils.VOLLEY_REQUESTS);
    }

    @Override
    public void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);

        if (resultCode == RESULT_OK && data != null) {

            Bitmap bitmap = null;
            ByteArrayOutputStream outputStream = new ByteArrayOutputStream();

            try {
                if (data.getData() != null) {
                    bitmap = MediaStore.Images.Media.getBitmap(getContentResolver(), data.getData());
                } else {
                    bitmap = (Bitmap) data.getExtras().get("data");
                }

                bitmap = getScaledImage(bitmap);
                bitmap.compress(Bitmap.CompressFormat.PNG, 100, outputStream);

            } catch (IOException e) {
                e.printStackTrace();
                Toast.makeText(this, "Image exception", Toast.LENGTH_SHORT).show();
            }
            switch (requestCode) {
                case AADHAR_IMAGE_FIRST_REQUEST:
                    ivAadharFront.setImageBitmap(bitmap);
                    imgAadharFront = Base64.encodeToString(outputStream.toByteArray(), Base64.DEFAULT);
                    break;
                case AADHAR_IMAGE_BACK_REQUEST:
                    ivAadharBack.setImageBitmap(bitmap);
                    imgAadharBack = Base64.encodeToString(outputStream.toByteArray(), Base64.DEFAULT);
                    break;
                case DL_IMAGE_REQUEST:
                    ivDL.setImageBitmap(bitmap);
                    imgDL = Base64.encodeToString(outputStream.toByteArray(), Base64.DEFAULT);
                    break;
                case PAN_IMAGE_REQUEST:
                    ivPan.setImageBitmap(bitmap);
                    imgPanCard = Base64.encodeToString(outputStream.toByteArray(), Base64.DEFAULT);
                    break;
                case PHOTO_IMAGE_REQUEST:
                    ivPhoto.setImageBitmap(bitmap);
                    imgPhoto = Base64.encodeToString(outputStream.toByteArray(), Base64.DEFAULT);
                    break;
                case CHEQUE_IMAGE_REQUEST:
                    ivCheque.setImageBitmap(bitmap);
                    imgCheque = Base64.encodeToString(outputStream.toByteArray(), Base64.DEFAULT);
                    break;
                case SIGNATURE_IMAGE_REQUEST:
                    ivSignature.setImageBitmap(bitmap);
                    imgSignature = Base64.encodeToString(outputStream.toByteArray(), Base64.DEFAULT);
                    break;
                case INSURANCE_IMAGE_REQUEST:
                    ivInsuranceDoc.setImageBitmap(bitmap);
                    imgInsuranceDoc = Base64.encodeToString(outputStream.toByteArray(), Base64.DEFAULT);
                    break;
            }

            submitData(false);
        }
    }

    private void submitData(final boolean finish) {
        if (Utils.isConnected(this)) {
            progressBar.setVisibility(View.VISIBLE);
            StringRequest strReq = new StringRequest(Request.Method.PUT, Utils.DOMAIN + "/api/v1/update-profile", new Response.Listener<String>() {
                @Override
                public void onResponse(String response) {
                    try {
                        progressBar.setVisibility(View.GONE);
                        JSONObject object = new JSONObject(response);
                        if (object.getString("status").equals("ok") && finish) {
                            Toast.makeText(MyProfileActivity.this, object.getString("message"), Toast.LENGTH_SHORT).show();
                        }
                    } catch (JSONException e) {
                        Toast.makeText(MyProfileActivity.this, R.string.something_went_wrong, Toast.LENGTH_SHORT).show();
                    }
                }
            }, new Response.ErrorListener() {
                @RequiresApi(api = Build.VERSION_CODES.KITKAT)
                @Override
                public void onErrorResponse(VolleyError error) {
                    progressBar.setVisibility(View.GONE);
                    NetworkResponse networkResponse = error.networkResponse;
                    if (networkResponse != null) {
                        int errorCode = networkResponse.statusCode;
                        String responseBody = new String(error.networkResponse.data, StandardCharsets.UTF_8);
                        Log.d("DATA_ERR_", responseBody);
                    }
                }
            }) {
                @Override
                public Map<String, String> getHeaders() throws AuthFailureError {
                    HashMap<String, String> headers = new HashMap<>();
                    headers.put("Authorization", "Bearer " + sharedPreference.getRememberToken(MyProfileActivity.this));
                    return headers;
                }
                @Override
                protected Map<String, String> getParams() {
                    Map<String, String> params = new HashMap<>();
                    params.put("name", etName.getText().toString().trim());
                    params.put("father_name", etFatherName.getText().toString().trim());
                    params.put("dob", etDOB.getText().toString().trim());
                    params.put("email", etEmail.getText().toString().trim());
                    params.put("mobile", etMobile.getText().toString().trim());
                    params.put("alt_mobile", etAltMobile.getText().toString().trim());
                    params.put("cur_address", etCurrAddress.getText().toString().trim());
                    params.put("per_address", etPermanentAddress.getText().toString().trim());
                    params.put("village", etVillage.getText().toString().trim());
                    params.put("town", etTown.getText().toString().trim());
                    params.put("city", etCity.getText().toString().trim());
                    params.put("district", etDistrict.getText().toString().trim());
                    params.put("state", etState.getText().toString().trim());
                    params.put("pincode", etPinCode.getText().toString().trim());
                    String otherQualifications;
                    if (rgQualification.getCheckedRadioButtonId() == R.id.other) {
                        otherQualifications = etOtherQualification.getText().toString();
                    } else {
                        otherQualifications = ((RadioButton) findViewById(rgQualification.getCheckedRadioButtonId())).getText().toString();
                    }
                    params.put("qualification", otherQualifications);
                    /*
                     * Image upload
                    */
                    params.put("aadhar_front", imgAadharFront == null ? "" : imgAadharFront);
                    params.put("aadhar_back", imgAadharBack == null ? "" : imgAadharBack);
                    params.put("driving_license", imgDL == null ? "" : imgDL);
                    params.put("pan_card", imgPanCard == null ? "" : imgPanCard);
                    params.put("photo", imgPhoto == null ? "" : imgPhoto);
                    params.put("cheque", imgCheque == null ? "" : imgCheque);
                    params.put("signature", imgSignature == null ? "" : imgSignature);
                    params.put("insurance", imgInsuranceDoc == null ? "" : imgInsuranceDoc);
                    /* Services */
                    params.put("services", vendorServicesIds);
                    return params;
                }
            };
            strReq.setRetryPolicy(new DefaultRetryPolicy(0, DefaultRetryPolicy.DEFAULT_MAX_RETRIES, DefaultRetryPolicy.DEFAULT_BACKOFF_MULT));
            ApplicationController.getInstance().addToRequestQueue(strReq, Utils.VOLLEY_REQUESTS);

        } else {
            Toast.makeText(this, R.string.connection_error, Toast.LENGTH_SHORT).show();
        }
    }

    private Bitmap getScaledImage(Bitmap bitmap) {
        int width = bitmap.getWidth();
        int height = bitmap.getHeight();
        int maxWidth = 800, maxHeight = 800;
        float ratioBitmap = (float) width / (float) height;
        float ratioMax = (float) maxWidth / (float) maxHeight;
        int finalWidth = maxWidth;
        int finalHeight = maxHeight;
        if (ratioMax > ratioBitmap) {
            finalWidth = (int) ((float)maxHeight * ratioBitmap);
        } else {
            finalHeight = (int) ((float)maxWidth / ratioBitmap);
        }
        bitmap = Bitmap.createScaledBitmap(bitmap, finalWidth, finalHeight, true);
        return bitmap;
    }

    private boolean hasPermission() {
        if (Build.VERSION.SDK_INT >= android.os.Build.VERSION_CODES.M) {
            return (ContextCompat.checkSelfPermission(this, Manifest.permission.READ_EXTERNAL_STORAGE) == PackageManager.PERMISSION_GRANTED)
                    && (ContextCompat.checkSelfPermission(this, Manifest.permission.WRITE_EXTERNAL_STORAGE) == PackageManager.PERMISSION_GRANTED)
                    && (ContextCompat.checkSelfPermission(this, Manifest.permission.CAMERA) == PackageManager.PERMISSION_GRANTED);
        } else {
            return true;
        }
    }

    private void askPermission() {
        if (Build.VERSION.SDK_INT >= android.os.Build.VERSION_CODES.M) {
            ActivityCompat.requestPermissions(this, new String[] {
                            Manifest.permission.READ_EXTERNAL_STORAGE,
                            Manifest.permission.WRITE_EXTERNAL_STORAGE,
                            Manifest.permission.CAMERA
                    },
                    STORAGE_PERMISSION_REQUEST);
        }
    }

    public String getFileNameByUri(Context context, Uri uri) {
        String fileName = "unknown";
        Uri filePathUri = uri;
        if (uri.getScheme().compareTo("content") == 0) {
            Cursor cursor = getContentResolver().query(uri, null, null,
                    null, null);
            if (cursor != null && cursor.moveToFirst()) {
                int column_index =
                        cursor.getColumnIndex(MediaStore.Images.Media.DATA);
                filePathUri = Uri.parse(cursor.getString(column_index));
                if (filePathUri == null) {
                    fileName = "xxx.png";//load a default Image from server
                } else {
                    fileName = filePathUri.getLastPathSegment().toString();
                }
            }
        } else if (uri.getScheme().compareTo("file") == 0) {
            fileName = filePathUri.getLastPathSegment().toString();
        } else {
            fileName = fileName + "_" + filePathUri.getLastPathSegment();
        }
        return fileName;
    }

    @Override
    public void onPaymentSuccess(String s, PaymentData paymentData) {
        if (currentPaymentType.equals("Membership Fee")) updateFeeStatusOnServer("membership_fee");
        else if (currentPaymentType.equals("Wallet Amount")) updateFeeStatusOnServer("wallet_amount");
    }

    private void updateFeeStatusOnServer(final String paymentType) {
        StringRequest strReq = new StringRequest(Request.Method.POST, Utils.DOMAIN + "/api/v1/update-vendor-payment", new Response.Listener<String>() {
            @Override
            public void onResponse(String response) {
                try {
                    JSONObject jsonObject = new JSONObject(response);
                    if (paymentType.equals("membership_fee")) {
                        btnEntryFee.setVisibility(View.GONE);
                        Toast.makeText(MyProfileActivity.this, "Membership fee has been paid successfully", Toast.LENGTH_LONG).show();
                    } else if (paymentType.equals("wallet_amount")) {
                        ((TextView)findViewById(R.id.value_wallet_balance)).setText(jsonObject.getString("wallet_balance"));
                        Toast.makeText(MyProfileActivity.this, "Wallet balance has been updated successfully", Toast.LENGTH_LONG).show();
                    }
                } catch (JSONException e) {
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
                        Toast.makeText(MyProfileActivity.this, error_.get(0).toString(), Toast.LENGTH_LONG).show();
                    } catch (JSONException e) {
                        Toast.makeText(MyProfileActivity.this, "Something went wrong on server", Toast.LENGTH_SHORT).show();
                        e.printStackTrace();
                    }
                }
            }
        }) {
            @Override
            public Map<String, String> getHeaders() {
                HashMap<String, String> headers = new HashMap<>();
                headers.put("Authorization", "Bearer " + sharedPreference.getRememberToken(MyProfileActivity.this));
                return headers;
            }
            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<>();
                params.put("type", paymentType);
                params.put("amount", String.valueOf(payment));
                return params;
            }
        };
        ApplicationController.getInstance().addToRequestQueue(strReq, Utils.VOLLEY_REQUESTS);
    }

    @Override
    public void onPaymentError(int i, String s, PaymentData paymentData) {
        Log.d("PAYMENT_ERROR__", s);
    }
}