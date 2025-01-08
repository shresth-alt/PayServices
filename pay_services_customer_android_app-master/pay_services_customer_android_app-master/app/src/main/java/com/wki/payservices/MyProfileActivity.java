package com.wki.payservices;

import androidx.annotation.NonNull;
import androidx.annotation.RequiresApi;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;

import android.content.Context;
import android.content.Intent;
import android.os.Build;
import android.os.Bundle;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.EditText;
import android.widget.ProgressBar;
import android.widget.RadioButton;
import android.widget.Toast;

import com.android.volley.AuthFailureError;
import com.android.volley.DefaultRetryPolicy;
import com.android.volley.NetworkResponse;
import com.android.volley.Request;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.StringRequest;
import com.wki.payservices.utility.Api;
import com.wki.payservices.utility.Constants;
import com.wki.payservices.utility.ErrorResponse;

import org.json.JSONException;
import org.json.JSONObject;

import java.nio.charset.StandardCharsets;
import java.util.HashMap;
import java.util.Map;

import io.github.inflationx.viewpump.ViewPumpContextWrapper;

public class MyProfileActivity extends AppCompatActivity {

    Toolbar toolbar;
    EditText etName, etEmail, etMobile, etAltMobile, etVillage, etLandmark, etCity, etPinCode, etState;
    SharedPreference sharedPreference = new SharedPreference();
    ProgressBar progressBar;

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
        if (getSupportActionBar() != null) {
            getSupportActionBar().setDisplayHomeAsUpEnabled(true);
            getSupportActionBar().setTitle(R.string.my_profile);
        }
        progressBar = findViewById(R.id.progressBar);
        etName = findViewById(R.id.ed_name);
        etEmail = findViewById(R.id.ed_email);
        etMobile = findViewById(R.id.ed_mobile);
        etAltMobile = findViewById(R.id.ed_alternate_mobile);
        etVillage = findViewById(R.id.ed_village);
        etLandmark = findViewById(R.id.ed_landmark);
        etCity = findViewById(R.id.ed_city);
        etState = findViewById(R.id.ed_state);
        etPinCode = findViewById(R.id.ed_pin_code);
        getCurrentUser();
    }

    private void getCurrentUser() {
        if (Utils.isConnected(this)) {
            progressBar.setVisibility(View.VISIBLE);
            StringRequest strReq = new StringRequest(Request.Method.GET, Utils.DOMAIN + "/api/v1/current-user", new Response.Listener<String>() {
                @Override
                public void onResponse(String response) {
                    try {
                        progressBar.setVisibility(View.GONE);
                        JSONObject object = new JSONObject(response);
                        etName.setText(object.getString("name"));
                        etName.setEnabled(false);
                        if (!object.isNull("email")) etEmail.setText(object.getString("email"));
                        etMobile.setText(object.getString("mobile"));
                        if (object.has("detail") && !object.isNull("detail")) {
                            JSONObject customerDetail = object.getJSONObject("detail");
                            if (!customerDetail.isNull("alt_mobile")) etAltMobile.setText(customerDetail.getString("alt_mobile"));
                            if (!customerDetail.isNull("village")) etVillage.setText(customerDetail.getString("village"));
                            if (!customerDetail.isNull("landmark")) etLandmark.setText(customerDetail.getString("landmark"));
                            if (!customerDetail.isNull("city"))  etCity.setText(customerDetail.getString("city"));
                            if (!customerDetail.isNull("state"))  etState.setText(customerDetail.getString("state"));
                            if (!customerDetail.isNull("pincode"))  etPinCode.setText(customerDetail.getString("pincode"));
                        }
                        etMobile.setEnabled(false);
                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                }
            }, new Response.ErrorListener() {
                @RequiresApi(api = Build.VERSION_CODES.KITKAT)
                @Override
                public void onErrorResponse(VolleyError error) {
                    progressBar.setVisibility(View.GONE);
                    Toast.makeText(MyProfileActivity.this, "Some error occured, please logout and login again", Toast.LENGTH_SHORT).show();
                }
            }) {
                @Override
                public Map<String, String> getHeaders() throws AuthFailureError {
                    HashMap<String, String> headers = new HashMap<>();
                    headers.put("Authorization", "Bearer " + sharedPreference.getRememberToken(MyProfileActivity.this));
                    return headers;
                }
            };
            ApplicationController.getInstance().addToRequestQueue(strReq, Utils.VOLLEY_REQUESTS);
        } else {
            Toast.makeText(this, R.string.connection_error, Toast.LENGTH_LONG).show();
        }
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        getMenuInflater().inflate(R.menu.my_profile, menu);
        return true;
    }

    private void submitData() {
        if (Utils.isConnected(this)) {
            progressBar.setVisibility(View.VISIBLE);
            StringRequest strReq = new StringRequest(Request.Method.PUT, Utils.DOMAIN + "/api/v1/update-profile", new Response.Listener<String>() {
                @Override
                public void onResponse(String response) {
                    try {
                        progressBar.setVisibility(View.GONE);
                        JSONObject object = new JSONObject(response);
                        Toast.makeText(MyProfileActivity.this, object.getString("message"), Toast.LENGTH_SHORT).show();
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
                    params.put("email", etEmail.getText().toString());
                    params.put("alt_mobile", etAltMobile.getText().toString());
                    params.put("village", etVillage.getText().toString());
                    params.put("landmark", etLandmark.getText().toString());
                    params.put("city", etCity.getText().toString());
                    params.put("state", etState.getText().toString());
                    params.put("pincode", etPinCode.getText().toString());
                    return params;
                }
            };
            strReq.setRetryPolicy(new DefaultRetryPolicy(0, DefaultRetryPolicy.DEFAULT_MAX_RETRIES, DefaultRetryPolicy.DEFAULT_BACKOFF_MULT));
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
            submitData();
        }
        return super.onOptionsItemSelected(item);
    }
}