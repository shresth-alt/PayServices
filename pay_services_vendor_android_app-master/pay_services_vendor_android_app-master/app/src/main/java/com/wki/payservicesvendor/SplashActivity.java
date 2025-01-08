package com.wki.payservicesvendor;

import android.content.Intent;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import android.util.Log;
import android.view.View;
import android.widget.Toast;

import androidx.annotation.RequiresApi;
import androidx.appcompat.app.AppCompatActivity;

import com.android.volley.AuthFailureError;
import com.android.volley.NetworkResponse;
import com.android.volley.Request;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.StringRequest;
import com.bumptech.glide.Glide;

import org.json.JSONException;
import org.json.JSONObject;

import java.nio.charset.StandardCharsets;
import java.util.HashMap;
import java.util.Map;

public class SplashActivity extends AppCompatActivity {

    SharedPreference sharedPreference = new SharedPreference();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_splash);
        if (sharedPreference.getLoggedIn(SplashActivity.this)) {
            getCurrentUser();
        } else {
            Intent i = new Intent(SplashActivity.this, LoginActivity.class);
            startActivity(i);
            finish();
        }
    }

    private void getCurrentUser() {
        if (Utils.isConnected(this)) {
            StringRequest strReq = new StringRequest(Request.Method.GET, Utils.DOMAIN + "/api/v1/current-user", new Response.Listener<String>() {
                @Override
                public void onResponse(String response) {
                    try {
                        Intent intent;
                        JSONObject object = new JSONObject(response);
                        if (object.getInt("is_verified") == 1) {
                            intent = new Intent(SplashActivity.this, OrdersActivity.class);
                            SharedPreference.setPref(SplashActivity.this, "VENDOR_VERIFIED", "true");
                        } else {
                            intent = new Intent(SplashActivity.this, MyProfileActivity.class);
                            SharedPreference.setPref(SplashActivity.this, "VENDOR_VERIFIED", "false");
                        }
                        startActivity(intent);
                        finish();
                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                }
            }, new Response.ErrorListener() {
                @RequiresApi(api = Build.VERSION_CODES.KITKAT)
                @Override
                public void onErrorResponse(VolleyError error) {
                    NetworkResponse networkResponse = error.networkResponse;
                    if (networkResponse != null) {
                        int errorCode = networkResponse.statusCode;
                        if (errorCode == 401) {
                            sharedPreference.logoutUser(SplashActivity.this);
                            Intent intent  = new Intent(SplashActivity.this, LoginActivity.class);
                            startActivity(intent);
                            finish();
                        } else {
                            String responseBody = new String(error.networkResponse.data, StandardCharsets.UTF_8);
                        }
                    }
                }
            }) {
                @Override
                public Map<String, String> getHeaders() throws AuthFailureError {
                    HashMap<String, String> headers = new HashMap<>();
                    headers.put("Authorization", "Bearer " + sharedPreference.getRememberToken(SplashActivity.this));
                    headers.put("Accept", "application/json");
                    return headers;
                }
            };
            ApplicationController.getInstance().addToRequestQueue(strReq, Utils.VOLLEY_REQUESTS);
        } else {
            Toast.makeText(this, R.string.connection_error, Toast.LENGTH_LONG).show();
        }
    }
}