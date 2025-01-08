package com.wki.payservices.utility;

import android.content.Context;
import com.android.volley.Request;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.StringRequest;
import com.wki.payservices.ApplicationController;
import com.wki.payservices.SharedPreference;
import com.wki.payservices.Utils;
import java.util.HashMap;
import java.util.Map;

public class Api {

    static Context context = ApplicationController.getInstance().getApplicationContext();
    static SharedPreference sharedPreference = new SharedPreference();

    public static void get(String url, DataListener dataListener, int resultCode) {
        StringRequest strReq = new StringRequest(
                Request.Method.GET,
                url,
                response-> dataListener.onData(response, resultCode),
                error-> dataListener.onError(error, resultCode)
        ) {
            @Override
            public Map<String, String> getHeaders() {
                HashMap<String, String> headers = new HashMap<>();
                headers.put("Authorization", "Bearer " + sharedPreference.getRememberToken(context));
                return headers;
            }
        };
        ApplicationController.getInstance().addToRequestQueue(strReq, Utils.VOLLEY_REQUESTS);
    }

    public static void post(String url, HashMap<String, String> params, DataListener dataListener, int resultCode) {
        StringRequest strReq = new StringRequest(
                Request.Method.POST,
                url,
                response-> dataListener.onData(response, resultCode),
                error-> dataListener.onError(error, resultCode)
        ) {
            @Override
            protected Map<String, String> getParams() {
                return params;
            }

            @Override
            public Map<String, String> getHeaders() {
                HashMap<String, String> headers = new HashMap<>();
                headers.put("Authorization", "Bearer " + sharedPreference.getRememberToken(context));
                return headers;
            }
        };
        ApplicationController.getInstance().addToRequestQueue(strReq, Utils.VOLLEY_REQUESTS);
    }

    public static void put(String url, HashMap<String, String> params, DataListener dataListener, int resultCode) {
        StringRequest strReq = new StringRequest(
                Request.Method.PUT,
                url,
                response-> dataListener.onData(response, resultCode),
                error-> dataListener.onError(error, resultCode)
        ) {
            @Override
            protected Map<String, String> getParams() {
                return params;
            }

            @Override
            public Map<String, String> getHeaders() {
                HashMap<String, String> headers = new HashMap<>();
                headers.put("Authorization", "Bearer " + sharedPreference.getRememberToken(context));
                return headers;
            }
        };
        ApplicationController.getInstance().addToRequestQueue(strReq, Utils.VOLLEY_REQUESTS);
    }

    public interface DataListener {
        void onData(String response, int resultCode);
        void onError(VolleyError volleyError, int resultCode);
    }
}
