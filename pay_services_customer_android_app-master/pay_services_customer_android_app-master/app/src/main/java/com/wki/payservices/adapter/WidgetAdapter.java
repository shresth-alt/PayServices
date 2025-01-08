package com.wki.payservices.adapter;

import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.text.Html;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AlertDialog;
import androidx.recyclerview.widget.RecyclerView;

import com.android.volley.AuthFailureError;
import com.android.volley.DefaultRetryPolicy;
import com.android.volley.NetworkResponse;
import com.android.volley.Request;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.JsonObjectRequest;
import com.android.volley.toolbox.StringRequest;
import com.squareup.picasso.Picasso;
import com.wki.payservices.ApplicationController;
import com.wki.payservices.MainActivity;
import com.wki.payservices.R;
import com.wki.payservices.ServicesActivity;
import com.wki.payservices.SharedPreference;
import com.wki.payservices.Utils;
import com.wki.payservices.model.Service;
import com.wki.payservices.model.Widget;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

public class WidgetAdapter extends RecyclerView.Adapter<WidgetAdapter.ViewHolder>{

    private List<Widget> widgetList;
    Context context;

    SharedPreference sharedPreference = new SharedPreference();

    public WidgetAdapter(List<Widget> widgetList, Context context) {
        this.widgetList = widgetList;
        this.context = context;
    }

    @Override
    public ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        LayoutInflater layoutInflater = LayoutInflater.from(parent.getContext());
        View listItem= layoutInflater.inflate(R.layout.widget_item, parent, false);
        return new ViewHolder(listItem);
    }

    @Override
    public void onBindViewHolder(ViewHolder holder, final int position) {
        final Widget service = widgetList.get(position);
        holder.serviceName.setText(Html.fromHtml(service.getName()));
        String iconURL = Utils.CLOUDINARY + "w_" + Utils.dpToPx(50) + "/" + widgetList.get(position).getIcon();
        Picasso.get().load(iconURL).into(holder.serviceIcon);
        holder.itemView.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                enquiryPopup(position);
            }
        });
    }

    private void enquiryPopup(final int position) {
        AlertDialog.Builder mBuilder = new AlertDialog.Builder(context);
        mBuilder.setTitle("Fill the details below!");
        final View customLayout = ((MainActivity)context).getLayoutInflater().inflate(R.layout.extra_services_popup, null);
        final EditText edComments = customLayout.findViewById(R.id.comments);
        mBuilder.setView(customLayout);
        mBuilder.setPositiveButton("Submit", new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface arg0, int arg1) {
                if (!edComments.getText().toString().trim().equals("")) {
                    sendWidgetRequest(position, edComments.getText().toString());
                } else {
                    Toast.makeText(context, "Please enter a valid comment which would help us in understanding the requirements easily!", Toast.LENGTH_SHORT).show();
                }
            }
        });
        mBuilder.setNegativeButton("Cancel", new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int which) {

            }
        });
        AlertDialog alertDialog = mBuilder.create();
        alertDialog.show();
    }

    private void sendWidgetRequest(int position, String comments) {
        HashMap<String, String> params = new HashMap<>();
        params.put("widget_id", widgetList.get(position).getId());
        params.put("comments", comments);
        Toast.makeText(context, "Submitting data...", Toast.LENGTH_SHORT).show();
        JsonObjectRequest req = new JsonObjectRequest(Utils.DOMAIN + "/api/v1/home-widgets-requests", new JSONObject(params), new Response.Listener<JSONObject>() {
            @Override
            public void onResponse(JSONObject response) {
                Toast.makeText(context, "We have received your request! One of our representative will call you soon.", Toast.LENGTH_LONG).show();
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
                        JSONArray errors = errorObject.getJSONArray("errors");
                        Toast.makeText(context, errors.getString(0), Toast.LENGTH_LONG).show();
                    } catch (JSONException e) {
                        Toast.makeText(context, e.getMessage(), Toast.LENGTH_SHORT).show();
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
        req.setRetryPolicy(new DefaultRetryPolicy(DefaultRetryPolicy.DEFAULT_TIMEOUT_MS * 1000, DefaultRetryPolicy.DEFAULT_MAX_RETRIES, DefaultRetryPolicy.DEFAULT_BACKOFF_MULT));
        ApplicationController.getInstance().addToRequestQueue(req);
    }

    @Override
    public int getItemCount() {
        return widgetList.size();
    }

    public static class ViewHolder extends RecyclerView.ViewHolder {
        public ImageView serviceIcon;
        public TextView serviceName;
        public ViewHolder(View itemView) {
            super(itemView);
            this.serviceIcon = itemView.findViewById(R.id.service_icon);
            this.serviceName = itemView.findViewById(R.id.service_name);
        }
    }
}