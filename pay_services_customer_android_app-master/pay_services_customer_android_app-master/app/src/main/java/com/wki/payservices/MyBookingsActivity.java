package com.wki.payservices;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import android.content.Context;
import android.content.Intent;
import android.graphics.Rect;
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
import com.wki.payservices.adapter.OrdersAdapter;
import com.wki.payservices.model.Order;
import com.wki.payservices.ui.bookings.BookingsFragment;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.Objects;

import io.github.inflationx.viewpump.ViewPumpContextWrapper;

public class MyBookingsActivity extends AppCompatActivity {

    private Toolbar toolbar;

    private RecyclerView listView;
    ProgressBar progressBar;
    private List<Order> bookingList = new ArrayList<>();
    private SharedPreference sharedPreference = new SharedPreference();

    @Override
    protected void attachBaseContext(Context newBase) {
        super.attachBaseContext(ViewPumpContextWrapper.wrap(newBase));
    }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_my_bookings);
        toolbar = findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);
        listView = findViewById(R.id.bookings_list);
        progressBar = findViewById(R.id.progressBar);
        if (getSupportActionBar() != null) {
            getSupportActionBar().setDisplayHomeAsUpEnabled(true);
            setTitle("My Bookings");
        }
        loadBookings();
    }

    @Override
    public void onBackPressed() {
        super.onBackPressed();
    }

    @Override
    public boolean onOptionsItemSelected(@NonNull MenuItem item) {
        int id = item.getItemId();
        if (id == android.R.id.home) {
            finish();
        }
        return super.onOptionsItemSelected(item);
    }

    private void loadBookings() {
        StringRequest strReq = new StringRequest(Request.Method.GET, Utils.DOMAIN + "/api/v1/orders", new Response.Listener<String>() {
            @Override
            public void onResponse(String response) {
                try {
                    JSONArray ordersArray = new JSONArray(response);
                    for (int i=0; i<ordersArray.length(); i++) {
                        JSONObject order = ordersArray.getJSONObject(i);
                        Order orderObj = new Order(
                                order.getString("id"),
                                order.getString("service_name"),
                                order.getString("service_date"),
                                order.getString("status"),
                                order.getString("payment_status"),
                                order.getString("address"),
                                order.getString("city"),
                                order.getString("landmark"),
                                order.getString("postal_code")
                        );
                        orderObj.setRated(order.getInt("is_rated") != 0);
                        bookingList.add(orderObj);
                    }
                    OrdersAdapter ordersAdapter = new OrdersAdapter(bookingList, MyBookingsActivity.this);
                    listView.setHasFixedSize(true);
                    listView.setLayoutManager(new GridLayoutManager(MyBookingsActivity.this, 1));
                    listView.addItemDecoration(new ItemOffsetDecoration(4));
                    listView.setAdapter(ordersAdapter);
                    progressBar.setVisibility(View.GONE);
                } catch (JSONException e) {
                    Toast.makeText(MyBookingsActivity.this, e.getMessage(), Toast.LENGTH_SHORT).show();
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
                            sharedPreference.logoutUser(MyBookingsActivity.this);
                            Intent intent = new Intent(MyBookingsActivity.this, LoginActivity.class);
                            startActivity(intent);
                            finish();
                        } else {
                            JSONObject errorObject = new JSONObject(json);
                            JSONArray error_ = errorObject.getJSONArray("errors");
                            Toast.makeText(MyBookingsActivity.this, error_.get(0).toString(), Toast.LENGTH_LONG).show();
                        }
                    } catch (JSONException e) {
                        Toast.makeText(MyBookingsActivity.this, "Something went wrong on server", Toast.LENGTH_SHORT).show();
                        e.printStackTrace();
                    }
                }
            }
        }){
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                HashMap<String, String> headers = new HashMap<>();
                headers.put("Authorization", "Bearer " + sharedPreference.getRememberToken(MyBookingsActivity.this));
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