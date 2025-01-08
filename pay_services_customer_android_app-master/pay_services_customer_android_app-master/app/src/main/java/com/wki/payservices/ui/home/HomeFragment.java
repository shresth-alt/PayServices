package com.wki.payservices.ui.home;

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
import androidx.viewpager.widget.ViewPager;
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
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.Objects;

import com.wki.payservices.SharedPreference;
import com.wki.payservices.Utils;
import com.wki.payservices.adapter.ServicesAdapter;
import com.wki.payservices.adapter.ViewPagerAdapter;
import com.wki.payservices.adapter.WidgetAdapter;
import com.wki.payservices.model.Service;
import com.wki.payservices.model.Widget;

public class HomeFragment extends Fragment {

    private RecyclerView listView;
    private List<Service> serviceList = new ArrayList<>();
    private List<Widget> widgetsList = new ArrayList<>();
    private SharedPreference sharedPreference = new SharedPreference();
    private ActionListener listener;
    Context context;
    ProgressBar progressBar;
    ViewPager offerPager;
    private ViewPagerAdapter mAdapter;
    com.google.android.material.tabs.TabLayout dots;

    public View onCreateView(@NonNull LayoutInflater inflater,
                             ViewGroup container, Bundle savedInstanceState) {
        View root = inflater.inflate(R.layout.fragment_home, container, false);
        listView = root.findViewById(R.id.widgets);
        progressBar = root.findViewById(R.id.progressBar);
        offerPager = root.findViewById(R.id.offers);
        dots = root.findViewById(R.id.dots);
        loadServicesAndWidgets();
        return root;
    }

    private void loadServicesAndWidgets() {
        StringRequest strReq = new StringRequest(Request.Method.GET, Utils.DOMAIN + "/api/v1/home-widgets-services", new Response.Listener<String>() {
            @Override
            public void onResponse(String response) {
                try {
                    JSONObject object = new JSONObject(response);
                    JSONObject data = object.getJSONObject("data");
                    JSONArray services = data.getJSONArray("services");
                    for (int i = 0; i < services.length(); i++) {
                        JSONObject service = services.getJSONObject(i);
                        /* Using icon attribute as slider image **/
                        serviceList.add(new Service(
                                service.getString("id"),
                                service.getString("name"),
                                service.getJSONObject("slider_image").getString("public_id") + "." +
                                        service.getJSONObject("slider_image").getString("format")
                        ));
                    }
                    JSONArray widgets = data.getJSONArray("widgets");
                    for (int i = 0; i < widgets.length(); i++) {
                        JSONObject widget = widgets.getJSONObject(i);
                        widgetsList.add(new Widget(
                                widget.getString("id"),
                                widget.getString("title"),
                                widget.getJSONObject("image").getString("public_id") + "." +
                                        widget.getJSONObject("image").getString("format")
                        ));
                    }
                    setUPSlider();
                    setUpWidgets();
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
        }){
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

    private void setUPSlider() {
        if (!serviceList.isEmpty()) {
            mAdapter = new ViewPagerAdapter(getContext(), serviceList);
            offerPager.setAdapter(mAdapter);
            offerPager.setCurrentItem(0);
            if (serviceList.size() > 1) {
                dots.setVisibility(View.VISIBLE);
                dots.setupWithViewPager(offerPager, true);
            }
        }
    }

    private void setUpWidgets() {
        if (!widgetsList.isEmpty()) {
            WidgetAdapter widgetAdapter = new WidgetAdapter(widgetsList, context);
            listView.setHasFixedSize(true);
            listView.setLayoutManager(new GridLayoutManager(context, 3));
            listView.addItemDecoration(new HomeFragment.ItemOffsetDecoration(0));
            listView.setAdapter(widgetAdapter);
            progressBar.setVisibility(View.GONE);
        }
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