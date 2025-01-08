package com.wki.payservicesvendor.ui.main;

import android.content.Context;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ProgressBar;
import android.widget.Toast;
import androidx.annotation.NonNull;
import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import com.android.volley.AuthFailureError;
import com.android.volley.NetworkResponse;
import com.android.volley.Request;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.StringRequest;
import com.wki.payservicesvendor.ActionListener;
import com.wki.payservicesvendor.ApplicationController;
import com.wki.payservicesvendor.BookingsActivity;
import com.wki.payservicesvendor.R;
import com.wki.payservicesvendor.SharedPreference;
import com.wki.payservicesvendor.Utils;
import com.wki.payservicesvendor.adapter.OrdersAdapter;
import com.wki.payservicesvendor.model.Order;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

/**
 * A placeholder fragment containing a simple view.
 */
public class BookingFragment extends Fragment {

    private List<Order> bookingList = new ArrayList<>();
    private SharedPreference sharedPreference = new SharedPreference();
    OrdersAdapter ordersAdapter;
    RecyclerView listView;
    String type = "";
    ProgressBar progressBar;
    public ActionListener listener;
    Context context;

    public BookingFragment(String type) {
        this.type = type;
    }

    @Override
    public View onCreateView(
            @NonNull LayoutInflater inflater, ViewGroup container,
            Bundle savedInstanceState) {
        View root = inflater.inflate(R.layout.fragment_orders, container, false);
        listView = root.findViewById(R.id.bookings_list);
        progressBar = root.findViewById(R.id.progressBar);
        loadBookings(false);
        return root;
    }

    @Override
    public void onAttach(Context context) {
        super.onAttach(context);
        try {
            listener = (ActionListener) context;
        } catch (ClassCastException castException) {
            /** The activity does not implement the listener. */
        }
    }

    public void loadBookings(final boolean refresh) {
        StringRequest strReq = new StringRequest(Request.Method.GET, Utils.DOMAIN + "/api/v1/orders", new Response.Listener<String>() {
            @Override
            public void onResponse(String response) {
                try {
                    JSONArray ordersArray = new JSONArray(response);
                    bookingList.clear();
                    for (int i=0; i<ordersArray.length(); i++) {
                        JSONObject order = ordersArray.getJSONObject(i);
                        if (type.equals(order.getString("status"))) {
                            Order order1 = new Order(
                                    order.getString("id"),
                                    order.getString("service_name"),
                                    order.getString("service_date"),
                                    order.getString("status"),
                                    order.getString("payment_status"),
                                    order.getString("payment_method"),
                                    order.getString("address"),
                                    order.getString("city"),
                                    order.getString("landmark"),
                                    order.getString("postal_code")
                            );
                            order1.setCustomerName(order.getString("customer_name"));
                            order1.setCustomerMobile(order.getString("customer_mobile"));
                            if (!order.isNull("comments")) order1.setOrderComments(order.getString("comments"));
                            order1.setCustomerMobile(order.getString("customer_mobile"));
                            bookingList.add(order1);
                        }
                    }
                    if (!refresh) {
                        ordersAdapter = new OrdersAdapter(bookingList, getContext(), BookingFragment.this);
                        listView.setHasFixedSize(true);
                        listView.setLayoutManager(new GridLayoutManager(getContext(), 1));
                        listView.addItemDecoration(new BookingsActivity.ItemOffsetDecoration(10));
                        listView.setAdapter(ordersAdapter);
                        progressBar.setVisibility(View.GONE);
                    } else {
                        listView.setAdapter(ordersAdapter);
                        ordersAdapter.notifyDataSetChanged();
                    }
                } catch (JSONException e) {
                    Toast.makeText(getContext(), e.getMessage(), Toast.LENGTH_SHORT).show();
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
                        Toast.makeText(getContext(), error_.get(0).toString(), Toast.LENGTH_LONG).show();
                    } catch (JSONException e) {
                        Toast.makeText(getContext(), "Something went wrong on server", Toast.LENGTH_SHORT).show();
                        e.printStackTrace();
                    }
                }
            }
        }){
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                HashMap<String, String> headers = new HashMap<>();
                headers.put("Authorization", "Bearer " + sharedPreference.getRememberToken(getContext()));
                return headers;
            }
        };
        strReq.setShouldCache(false);
        ApplicationController.getInstance().addToRequestQueue(strReq, Utils.VOLLEY_REQUESTS);
    }
}