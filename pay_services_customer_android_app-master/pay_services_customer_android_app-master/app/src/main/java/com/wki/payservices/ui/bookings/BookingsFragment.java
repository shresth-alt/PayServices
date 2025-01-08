package com.wki.payservices.ui.bookings;

import android.content.Context;
import android.graphics.Rect;
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
import com.wki.payservices.ActionListener;
import com.wki.payservices.ApplicationController;
import com.wki.payservices.R;
import com.wki.payservices.SharedPreference;
import com.wki.payservices.Utils;
import com.wki.payservices.adapter.OrdersAdapter;
import com.wki.payservices.model.Order;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

public class BookingsFragment extends Fragment {

    Context context;
    private RecyclerView listView;
    ProgressBar progressBar;
    private ActionListener listener;
    private List<Order> bookingList = new ArrayList<>();
    private SharedPreference sharedPreference = new SharedPreference();

    public View onCreateView(@NonNull LayoutInflater inflater,
                             ViewGroup container, Bundle savedInstanceState) {
        View root = inflater.inflate(R.layout.fragment_bookings, container, false);
        listView = root.findViewById(R.id.bookings_list);
        progressBar = root.findViewById(R.id.progressBar);
        loadBookings();
        return root;
    }

    private void loadBookings() {
        StringRequest strReq = new StringRequest(Request.Method.GET, Utils.DOMAIN + "/api/v1/orders", new Response.Listener<String>() {
            @Override
            public void onResponse(String response) {
                try {
                    JSONArray ordersArray = new JSONArray(response);
                    for (int i=0; i<ordersArray.length(); i++) {
                        JSONObject order = ordersArray.getJSONObject(i);
                        bookingList.add(new Order(
                                order.getString("id"),
                                order.getString("service_name"),
                                order.getString("service_date"),
                                order.getString("status"),
                                order.getString("payment_status"),
                                order.getString("address"),
                                order.getString("city"),
                                order.getString("landmark"),
                                order.getString("postal_code")
                        ));
                    }
                    OrdersAdapter ordersAdapter = new OrdersAdapter(bookingList, context);
                    listView.setHasFixedSize(true);
                    listView.setLayoutManager(new GridLayoutManager(context, 1));
                    listView.addItemDecoration(new ItemOffsetDecoration(1));
                    listView.setAdapter(ordersAdapter);
                    progressBar.setVisibility(View.GONE);
                } catch (JSONException e) {
                    Toast.makeText(context, e.getMessage(), Toast.LENGTH_SHORT).show();
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
                        Toast.makeText(context, error_.get(0).toString(), Toast.LENGTH_LONG).show();
                    } catch (JSONException e) {
                        Toast.makeText(context, "Something went wrong on server", Toast.LENGTH_SHORT).show();
                        e.printStackTrace();
                    }
                }
            }
        }){
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                HashMap<String, String> headers = new HashMap<>();
                headers.put("Authorization", "Bearer " + sharedPreference.getRememberToken(context));
                return headers;
            }
        };
        strReq.setShouldCache(false);
        ApplicationController.getInstance().addToRequestQueue(strReq, Utils.VOLLEY_REQUESTS);
    }

    @Override
    public void onAttach(Context context) {
        super.onAttach(context);
        try {
            listener = (ActionListener) context;
            this.context = context;
        } catch (ClassCastException castException) {
            /** The activity does not implement the listener. */
        }
    }

    public class ItemOffsetDecoration extends RecyclerView.ItemDecoration {
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