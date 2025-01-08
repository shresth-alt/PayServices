package com.wki.payservices.utility;

import com.android.volley.Header;
import com.android.volley.NetworkResponse;
import com.android.volley.VolleyError;
import org.json.JSONException;
import org.json.JSONObject;
import java.nio.charset.StandardCharsets;
import java.util.List;
import java.util.Map;

public class ErrorResponse {

    VolleyError volleyError;
    boolean isCompleted = false;
    JSONObject errors;
    int statusCode;
    List<Header> allHeaders;
    Map<String, String> header;

    public ErrorResponse(VolleyError volleyError) {
        this.volleyError = volleyError;
        init();
    }

    private void init() {

        NetworkResponse response = volleyError.networkResponse;

        if (response != null && response.data != null) {

            String jsonError = new String(response.data, StandardCharsets.UTF_8);

            try {
                errors = new JSONObject(jsonError);
                statusCode = response.statusCode;
                allHeaders = response.allHeaders;
                header = response.headers;

                isCompleted = true;

            } catch (JSONException e) {
                e.printStackTrace();
            }
        }
    }

    public JSONObject getErrors() {
        return errors;
    }

    public boolean isCompleted() {
        return isCompleted;
    }

    public int getStatusCode() {
        return statusCode;
    }

    public List<Header> getAllHeaders() {
        return allHeaders;
    }

    public Map<String, String> getHeader() {
        return header;
    }
}
