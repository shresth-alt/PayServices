package com.wki.payservices.ui.services;

import android.content.Context;
import android.content.Intent;
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
import com.wki.payservices.LoginActivity;
import com.wki.payservices.R;
import com.wki.payservices.SharedPreference;
import com.wki.payservices.Utils;
import com.wki.payservices.adapter.ServicesAdapter;
import com.wki.payservices.model.Service;
import com.wki.payservices.ui.home.HomeFragment;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

public class ServicesFragment extends Fragment {

    private RecyclerView listView;
    private List<Service> serviceList = new ArrayList<>();
    private SharedPreference sharedPreference = new SharedPreference();
    private ActionListener listener;
    Context context;
    ProgressBar progressBar;

    public View onCreateView(@NonNull LayoutInflater inflater,
                             ViewGroup container, Bundle savedInstanceState) {
        View root = inflater.inflate(R.layout.fragment_services, container, false);
        listView = root.findViewById(R.id.services_list);
        progressBar = root.findViewById(R.id.progressBar);
        loadServices();
        return root;
    }

    private void loadServices() {
        StringRequest strReq = new StringRequest(Request.Method.GET, Utils.DOMAIN + "/api/v1/services", new Response.Listener<String>() {
            @Override
            public void onResponse(String response) {
                try {
                    JSONObject object = new JSONObject(response);
                    JSONArray servicesArray = object.getJSONArray("data");
                    for (int i = 0; i < servicesArray.length(); i++) {
                        JSONObject service = servicesArray.getJSONObject(i);
                        serviceList.add(new Service(
                                service.getString("id"),
                                service.getString("name"),
                                service.getJSONObject("image").getString("public_id") + "." +
                                        service.getJSONObject("image").getString("format")
                        ));
                    }
                    ServicesAdapter servicesAdapter = new ServicesAdapter(serviceList, context);
                    listView.setHasFixedSize(true);
                    listView.setLayoutManager(new GridLayoutManager(context, 2));
                    listView.addItemDecoration(new HomeFragment.ItemOffsetDecoration(1));
                    listView.setAdapter(servicesAdapter);
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
                        if (response.statusCode == 401) {
                            /* Logout user */
                            sharedPreference.logoutUser(context);
                            Intent intent = new Intent(context, LoginActivity.class);
                            startActivity(intent);
                            requireActivity().finish();
                        } else {
                            JSONObject errorObject = new JSONObject(json);
                            JSONArray error_ = errorObject.getJSONArray("errors");
                            Toast.makeText(context, error_.get(0).toString(), Toast.LENGTH_LONG).show();
                        }
                    } catch (JSONException e) {
                        Toast.makeText(context, "Something went wrong on server", Toast.LENGTH_SHORT).show();
                        e.printStackTrace();
                    }
                }
            }
        }) {
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                HashMap<String, String> headers = new HashMap<>();
                headers.put("Authorization", "Bearer " + sharedPreference.getRememberToken(context));
                headers.put("Accept", "application/json");
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