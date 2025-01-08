package com.wki.payservices;

import androidx.appcompat.app.AppCompatActivity;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;
import com.android.volley.NetworkResponse;
import com.android.volley.Request;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.StringRequest;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import java.util.HashMap;
import java.util.Map;
import io.github.inflationx.viewpump.ViewPumpContextWrapper;

public class LoginActivity extends AppCompatActivity {

    SharedPreference sharedPreference = new SharedPreference();
    EditText editPhone, editOTP, editName;
    String otp = null;

    @Override
    protected void attachBaseContext(Context newBase) {
        super.attachBaseContext(ViewPumpContextWrapper.wrap(newBase));
    }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login);
        editName = findViewById(R.id.edt_name);
        editPhone = findViewById(R.id.edt_phone);
        editOTP = findViewById(R.id.edt_otp);
        findViewById(R.id.btn_login).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if (otp == null) {
                    sendOTP("login");
                    ((Button)findViewById(R.id.btn_login)).setText(R.string.label_sending_top);
                    findViewById(R.id.btn_login).setEnabled(false);
                }
                else doLogin();
            }
        });
        findViewById(R.id.btn_signup).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if (otp == null) {
                    ((Button)findViewById(R.id.btn_signup)).setText(R.string.label_sending_top);
                    findViewById(R.id.btn_signup).setEnabled(false);
                    sendOTP("registration");
                }
                else doSignUP();
            }
        });
        enableLoginView();
        ((Button)findViewById(R.id.btn_login)).setTransformationMethod(null);
        ((Button)findViewById(R.id.btn_signup)).setTransformationMethod(null);
        (findViewById(R.id.btn_registration_view)).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                enableRegistrationView();
            }
        });
        (findViewById(R.id.btn_login_view)).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                enableLoginView();
            }
        });
    }

    private void enableLoginView() {
        findViewById(R.id.sign_in_info).setVisibility(View.GONE);
        findViewById(R.id.btn_signup).setVisibility(View.GONE);
        findViewById(R.id.edt_name).setVisibility(View.GONE);
        findViewById(R.id.edt_otp).setVisibility(View.GONE);
        findViewById(R.id.btn_signup).setVisibility(View.GONE);

        findViewById(R.id.sign_up_info).setVisibility(View.VISIBLE);
        findViewById(R.id.btn_login).setVisibility(View.VISIBLE);
        otp = null;
    }

    private void enableRegistrationView() {
        findViewById(R.id.sign_in_info).setVisibility(View.VISIBLE);
        findViewById(R.id.btn_signup).setVisibility(View.VISIBLE);
        findViewById(R.id.edt_name).setVisibility(View.VISIBLE);
        findViewById(R.id.edt_otp).setVisibility(View.GONE);

        findViewById(R.id.sign_up_info).setVisibility(View.GONE);
        findViewById(R.id.btn_login).setVisibility(View.GONE);
        otp = null;
    }

    private void sendOTP(final String type) {
        StringRequest strReq = new StringRequest(Request.Method.POST, Utils.DOMAIN + "/api/v1/send-otp", response -> {
            try {
                JSONObject object = new JSONObject(response);
                otp = object.getString("otp");
                findViewById(R.id.edt_otp).setVisibility(View.VISIBLE);
                if (type.equals("login")) {
                    ((Button)findViewById(R.id.btn_login)).setText(R.string.login);
                    findViewById(R.id.btn_login).setEnabled(true);
                } else {
                    findViewById(R.id.btn_signup).setEnabled(true);
                    ((Button)findViewById(R.id.btn_signup)).setText(R.string.label_sign_up);
                }
            } catch (JSONException e) {
                e.printStackTrace();
            }
        }, error -> {
            if (type.equals("login")) {
                ((Button)findViewById(R.id.btn_login)).setText(R.string.login);
                findViewById(R.id.btn_login).setEnabled(true);
            } else {
                findViewById(R.id.btn_signup).setEnabled(true);
                ((Button)findViewById(R.id.btn_signup)).setText(R.string.label_sign_up);
            }
            String json;
            NetworkResponse response = error.networkResponse;
            if (response != null && response.data != null) {
                json = new String(response.data);
                try {
                    JSONObject errorObject = new JSONObject(json);
                    JSONArray error_ = errorObject.getJSONArray("errors");
                    Toast.makeText(LoginActivity.this, error_.get(0).toString(), Toast.LENGTH_LONG).show();
                } catch (JSONException e) {
                    Toast.makeText(LoginActivity.this, "Something went wrong on server", Toast.LENGTH_SHORT).show();
                    e.printStackTrace();
                }
            }
        }) {
            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<>();
                params.put("mobile", editPhone.getText().toString());
                params.put("type", type);
                return params;
            }
        };
        ApplicationController.getInstance().addToRequestQueue(strReq, Utils.VOLLEY_REQUESTS);
    }

    private void doLogin() {
        if (!editOTP.getText().toString().equals(otp)) {
            Toast.makeText(this, "You entered wrong OTP. Please check it and try again", Toast.LENGTH_SHORT).show();
            return;
        }
        findViewById(R.id.btn_login).setEnabled(false);
        ((Button)findViewById(R.id.btn_login)).setText(R.string.label_logging_in);
        StringRequest strReq = new StringRequest(Request.Method.POST, Utils.DOMAIN + "/api/v1/authenticate", new Response.Listener<String>() {
            @Override
            public void onResponse(String response) {
                try {
                    JSONObject object = new JSONObject(response);
                    sharedPreference.setUserId(LoginActivity.this, object.getInt("id"));
                    SharedPreference.setPref(LoginActivity.this, "name", object.getString("name"));
                    sharedPreference.setLoggedIn(LoginActivity.this, true);
                    sharedPreference.setRememberToken(LoginActivity.this, object.getString("api_token"));
                    Intent intent ;
                    intent = new Intent(LoginActivity.this, MainActivity.class);
                    startActivity(intent);
                    finish();
                } catch (JSONException e) {
                    e.printStackTrace();
                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError error) {
                findViewById(R.id.btn_login).setEnabled(true);
                ((Button)findViewById(R.id.btn_login)).setText(R.string.login);
                String json;
                NetworkResponse response = error.networkResponse;
                if (response != null && response.data != null) {
                    json = new String(response.data);
                    try {
                        JSONObject errorObject = new JSONObject(json);
                        JSONArray error_ = errorObject.getJSONArray("errors");
                        Toast.makeText(LoginActivity.this, error_.get(0).toString(), Toast.LENGTH_LONG).show();
                    } catch (JSONException e) {
                        Toast.makeText(LoginActivity.this, "Something went wrong on server", Toast.LENGTH_SHORT).show();
                        e.printStackTrace();
                    }
                }
            }
        }) {
            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<>();
                params.put("mobile", editPhone.getText().toString());
                params.put("otp", editOTP.getText().toString());
                return params;
            }
        };
        ApplicationController.getInstance().addToRequestQueue(strReq, Utils.VOLLEY_REQUESTS);
    }

    private void doSignUP() {
        if (!editOTP.getText().toString().equals(otp)) {
            Toast.makeText(this, "You entered wrong OTP. Please check it and try again", Toast.LENGTH_SHORT).show();
            return;
        }
        findViewById(R.id.btn_signup).setEnabled(false);
        ((Button)findViewById(R.id.btn_signup)).setText(R.string.label_signing_up);
        StringRequest strReq = new StringRequest(Request.Method.POST, Utils.DOMAIN + "/api/v1/users", new Response.Listener<String>() {
            @Override
            public void onResponse(String response) {
                try {
                    JSONObject object = new JSONObject(response);
                    sharedPreference.setUserId(LoginActivity.this, object.getInt("id"));
                    SharedPreference.setPref(LoginActivity.this, "name", object.getString("name"));
                    SharedPreference.setPref(LoginActivity.this, "mobile", object.getString("mobile"));
                    sharedPreference.setLoggedIn(LoginActivity.this, true);
                    sharedPreference.setRememberToken(LoginActivity.this, object.getString("api_token"));
                    Intent intent ;
                    intent = new Intent(LoginActivity.this, MainActivity.class);
                    startActivity(intent);
                    finish();
                } catch (JSONException e) {
                    e.printStackTrace();
                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError error) {
                findViewById(R.id.btn_signup).setEnabled(true);
                ((Button)findViewById(R.id.btn_signup)).setText(R.string.label_sign_up);
                String json;
                NetworkResponse response = error.networkResponse;
                if (response != null && response.data != null) {
                    json = new String(response.data);
                    try {
                        JSONObject errorObject = new JSONObject(json);
                        JSONArray error_ = errorObject.getJSONArray("errors");
                        Toast.makeText(LoginActivity.this, error_.get(0).toString(), Toast.LENGTH_LONG).show();
                    } catch (JSONException e) {
                        Toast.makeText(LoginActivity.this, "Something went wrong on server", Toast.LENGTH_SHORT).show();
                        e.printStackTrace();
                    }
                }
            }
        }) {
            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<>();
                params.put("name", editName.getText().toString().trim());
                params.put("mobile", editPhone.getText().toString().trim());
                params.put("type", "customer");
                return params;
            }
        };
        ApplicationController.getInstance().addToRequestQueue(strReq, Utils.VOLLEY_REQUESTS);
    }

}