package com.wki.payservicesvendor;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import android.content.Context;
import android.os.Bundle;
import android.util.Log;
import android.view.MenuItem;
import android.view.View;
import android.widget.ProgressBar;
import android.widget.Toast;

import com.android.volley.AuthFailureError;
import com.android.volley.NetworkResponse;
import com.android.volley.Request;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.StringRequest;
import com.wki.payservicesvendor.adapter.OrdersAdapter;
import com.wki.payservicesvendor.adapter.VendorPaymentAdapter;
import com.wki.payservicesvendor.model.Order;
import com.wki.payservicesvendor.model.VendorPayment;
import com.wki.payservicesvendor.ui.main.BookingFragment;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import io.github.inflationx.viewpump.ViewPumpContextWrapper;

public class AccountStatements extends AppCompatActivity {

    private List<VendorPayment> statementsList = new ArrayList<>();
    private SharedPreference sharedPreference = new SharedPreference();
    VendorPaymentAdapter vendorPaymentAdapter;
    RecyclerView listView;
    ProgressBar progressBar;
    private Toolbar toolbar;

    @Override
    protected void attachBaseContext(Context newBase) {
        super.attachBaseContext(ViewPumpContextWrapper.wrap(newBase));
    }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_account_statements);
        toolbar = findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);
        if (getSupportActionBar() != null) {
            getSupportActionBar().setDisplayHomeAsUpEnabled(true);
            getSupportActionBar().setTitle("Account Statements");
        }
        listView = findViewById(R.id.statements_list);
        progressBar = findViewById(R.id.progressBar);
        loadStatements();
    }

    public void loadStatements() {
        StringRequest strReq = new StringRequest(Request.Method.GET, Utils.DOMAIN + "/api/v1/account-statements", new Response.Listener<String>() {
            @Override
            public void onResponse(String response) {
                try {
                    JSONArray statementsArray = new JSONArray(response);
                    statementsList.clear();
                    for (int i=0; i<statementsArray.length(); i++) {
                        JSONObject statement = statementsArray.getJSONObject(i);
                        statementsList.add(new VendorPayment(
                                statement.getString("order_id"),
                                statement.getString("pay_type"),
                                statement.getString("created_at"),
                                statement.getDouble("amount"),
                                statement.getDouble("order_amount"),
                                statement.getDouble("service_charge"),
                                statement.getDouble("commission"),
                                statement.getDouble("commission_gst")
                        ));
                    }
                    vendorPaymentAdapter = new VendorPaymentAdapter(statementsList, AccountStatements.this);
                    listView.setHasFixedSize(true);
                    listView.setLayoutManager(new GridLayoutManager(AccountStatements.this, 1));
                    listView.addItemDecoration(new BookingsActivity.ItemOffsetDecoration(10));
                    listView.setAdapter(vendorPaymentAdapter);
                    progressBar.setVisibility(View.GONE);
                } catch (JSONException e) {
                    Toast.makeText(AccountStatements.this, e.getMessage(), Toast.LENGTH_SHORT).show();
                    e.printStackTrace();
                    progressBar.setVisibility(View.GONE);
                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError error) {
                progressBar.setVisibility(View.GONE);
                String json;
                NetworkResponse response = error.networkResponse;
                if (response != null && response.data != null) {
                    json = new String(response.data);
                    try {
                        JSONObject errorObject = new JSONObject(json);
                        JSONArray error_ = errorObject.getJSONArray("errors");
                        Toast.makeText(AccountStatements.this, error_.get(0).toString(), Toast.LENGTH_LONG).show();
                    } catch (JSONException e) {
                        Toast.makeText(AccountStatements.this, "Something went wrong on server", Toast.LENGTH_SHORT).show();
                        e.printStackTrace();
                    }
                }
            }
        }){
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                HashMap<String, String> headers = new HashMap<>();
                headers.put("Authorization", "Bearer " + sharedPreference.getRememberToken(AccountStatements.this));
                return headers;
            }
        };
        strReq.setShouldCache(false);
        ApplicationController.getInstance().addToRequestQueue(strReq, Utils.VOLLEY_REQUESTS);
    }

    @Override
    public boolean onOptionsItemSelected(@NonNull MenuItem item) {
        int id = item.getItemId();
        if (id == android.R.id.home) {
            finish();
        }
        return super.onOptionsItemSelected(item);
    }
}