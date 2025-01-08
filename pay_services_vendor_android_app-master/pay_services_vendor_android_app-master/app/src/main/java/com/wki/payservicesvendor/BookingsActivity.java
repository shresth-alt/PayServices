package com.wki.payservicesvendor;

import android.content.Context;
import android.graphics.Rect;
import android.os.Bundle;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.ProgressBar;
import android.widget.Toast;
import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import com.android.volley.AuthFailureError;
import com.android.volley.DefaultRetryPolicy;
import com.android.volley.NetworkResponse;
import com.android.volley.Request;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.JsonObjectRequest;
import com.android.volley.toolbox.StringRequest;
import com.wki.payservicesvendor.adapter.OrdersAdapter;
import com.wki.payservicesvendor.model.Order;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import io.github.inflationx.viewpump.ViewPumpContextWrapper;

public class BookingsActivity extends AppCompatActivity {

    private Toolbar toolbar;
    OrdersAdapter ordersAdapter;

    private RecyclerView listView;
    ProgressBar progressBar;
    private List<com.wki.payservicesvendor.model.Order> bookingList = new ArrayList<>();
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
            setTitle("Bookings");
        }
        loadBookings(false);
        sendTokenOnServer();
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        getMenuInflater().inflate(R.menu.booking_sc_menu, menu);
        return true;
    }

    private void sendTokenOnServer() {
        if (Utils.isConnected(BookingsActivity.this)) {
            final String URL = Utils.DOMAIN + "/api/v1/update-user-token";
            HashMap<String, String> params = new HashMap<>();
            params.put("token", String.valueOf(sharedPreference.getFCMToken(BookingsActivity.this)));
            JsonObjectRequest req = new JsonObjectRequest(URL, new JSONObject(params),
                    new Response.Listener<JSONObject>() {
                        @Override
                        public void onResponse(JSONObject response) {
                            sharedPreference.setFcmUpdatedOnServer(BookingsActivity.this, true);
                        }
                    }, new Response.ErrorListener() {
                @Override
                public void onErrorResponse(VolleyError error) {

                }
            }){
                @Override
                public Map<String, String> getHeaders() throws AuthFailureError {
                    HashMap<String, String> headers = new HashMap<>();
                    headers.put("Authorization", "Bearer " + sharedPreference.getRememberToken(BookingsActivity.this));
                    return headers;
                }
            };
            req.setRetryPolicy(new DefaultRetryPolicy(DefaultRetryPolicy.DEFAULT_TIMEOUT_MS * 1000, DefaultRetryPolicy.DEFAULT_MAX_RETRIES, DefaultRetryPolicy.DEFAULT_BACKOFF_MULT));
            ApplicationController.getInstance().addToRequestQueue(req);
        }
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
        } else if (id == R.id.action_refresh) {
            loadBookings(true);
        }
        return super.onOptionsItemSelected(item);
    }

    private void loadBookings(final boolean refresh) {
        /*
        StringRequest strReq = new StringRequest(Request.Method.GET, Utils.DOMAIN + "/api/v1/orders", new Response.Listener<String>() {
            @Override
            public void onResponse(String response) {
                try {
                    JSONArray ordersArray = new JSONArray(response);
                    bookingList.clear();
                    for (int i=0; i<ordersArray.length(); i++) {
                        JSONObject order = ordersArray.getJSONObject(i);
                        bookingList.add(new Order(
                                order.getString("id"),
                                order.getString("service_name"),
                                order.getString("service_date"),
                                order.getString("status"),
                                order.getString("address"),
                                order.getString("city"),
                                order.getString("landmark"),
                                order.getString("postal_code")
                        ));
                    }
                    if (!refresh) {
                        ordersAdapter = new OrdersAdapter(bookingList, BookingsActivity.this);
                        listView.setHasFixedSize(true);
                        listView.setLayoutManager(new GridLayoutManager(BookingsActivity.this, 1));
                        listView.addItemDecoration(new ItemOffsetDecoration(10));
                        listView.setAdapter(ordersAdapter);
                        progressBar.setVisibility(View.GONE);
                    } else {
                        Toast.makeText(BookingsActivity.this, "Bookings refreshed", Toast.LENGTH_SHORT).show();
                        ordersAdapter.notifyDataSetChanged();
                    }
                    if (bookingList.isEmpty()) {
                        findViewById(R.id.no_bookings_found).setVisibility(View.VISIBLE);
                    } else {
                        findViewById(R.id.no_bookings_found).setVisibility(View.GONE);
                    }
                } catch (JSONException e) {
                    Toast.makeText(BookingsActivity.this, e.getMessage(), Toast.LENGTH_SHORT).show();
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
                        Toast.makeText(BookingsActivity.this, error_.get(0).toString(), Toast.LENGTH_LONG).show();
                    } catch (JSONException e) {
                        Toast.makeText(BookingsActivity.this, "Something went wrong on server", Toast.LENGTH_SHORT).show();
                        e.printStackTrace();
                    }
                }
            }
        }){
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                HashMap<String, String> headers = new HashMap<>();
                headers.put("Authorization", "Bearer " + sharedPreference.getRememberToken(BookingsActivity.this));
                return headers;
            }
        };
        strReq.setShouldCache(false);
        ApplicationController.getInstance().addToRequestQueue(strReq, Utils.VOLLEY_REQUESTS);
         */
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