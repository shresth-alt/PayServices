package com.wki.payservices.adapter;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.EditText;
import android.widget.RatingBar;
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
import com.wki.payservices.ApplicationController;
import com.wki.payservices.MainActivity;
import com.wki.payservices.MyBookingsActivity;
import com.wki.payservices.PaymentActivity;
import com.wki.payservices.R;
import com.wki.payservices.SharedPreference;
import com.wki.payservices.Utils;
import com.wki.payservices.model.Order;
import com.wki.payservices.utility.Api;
import com.wki.payservices.utility.Constants;
import com.wki.payservices.utility.ErrorResponse;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

public class OrdersAdapter extends RecyclerView.Adapter<OrdersAdapter.ViewHolder>{

    Context context;
    List<Order> ordersList;
    SharedPreference sharedPreference = new SharedPreference();

    public OrdersAdapter(List<Order> serviceList, Context context) {
        this.context = context;
        this.ordersList = serviceList;
    }

    @Override
    public ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        LayoutInflater layoutInflater = LayoutInflater.from(parent.getContext());
        View listItem = layoutInflater.inflate(R.layout.order_item, parent, false);
        return new ViewHolder(listItem);
    }

    @Override
    public void onBindViewHolder(ViewHolder holder, final int position) {
        final Order service = ordersList.get(position);
        holder.serviceName.setText(service.getServiceName());
        holder.serviceDate.setText(service.getServiceDate());
        holder.address.setText(service.getAddress());
        holder.serviceStatus.setText(service.getServiceStatus());
        if (service.getServiceStatus().equals("cancelled") || service.getServiceStatus().equals("completed")) {
            holder.cancelOrder.setVisibility(View.GONE);
        }
        if (service.getServiceStatus().equals("completed") && service.getPaymentStatus().equals("pending")) {
            holder.payAmount.setVisibility(View.VISIBLE);
        }
        if (service.getServiceStatus().equals("completed") && service.getPaymentStatus().equals("completed")) {
            holder.rateNow.setVisibility(View.VISIBLE);
        }
        holder.cancelOrder.setOnClickListener(v-> cancelOrderPopup(position));
        holder.rateNow.setOnClickListener(v-> addRating(position));
        holder.payAmount.setOnClickListener(v-> {
            if (Utils.isConnected(context)) {
                payForOrder(position);
            } else {
                Toast.makeText(context, "No internet connection", Toast.LENGTH_SHORT).show();
            }
        });
        if (service.isRated()) holder.rateNow.setVisibility(View.GONE);
    }

    EditText edCancellationReason;

    private void cancelOrderPopup(final int position) {
        AlertDialog.Builder mBuilder = new AlertDialog.Builder(context);
        mBuilder.setTitle("Do you really want to cancel booking?");
        final View customLayout = ((MyBookingsActivity)context).getLayoutInflater().inflate(R.layout.order_cancel_popup, null);
        edCancellationReason = customLayout.findViewById(R.id.cancellation_reason);
        mBuilder.setView(customLayout);
        mBuilder.setPositiveButton("yes", (arg0, arg1)-> {
            if (!edCancellationReason.getText().toString().trim().isEmpty()) {
                cancelOrder(position);
            } else {
                Toast.makeText(context, "Please enter a valid reason for order cancellation", Toast.LENGTH_SHORT).show();
            }
        });
        mBuilder.setNegativeButton("no", null);
        AlertDialog alertDialog = mBuilder.create();
        alertDialog.show();
    }

    private void cancelOrder(final int position) {
        final String url = Utils.DOMAIN + "/api/v1/cancel-order/"+ ordersList.get(position).getOrderNumber();
        HashMap<String, String> params = new HashMap<>();
        params.put("cancellation_reason", edCancellationReason.getText().toString().trim());
        params.put("order_status", "cancelled");
        Toast.makeText(context, "Cancelling order...", Toast.LENGTH_SHORT).show();
        JsonObjectRequest req = new JsonObjectRequest(
                Request.Method.PUT,
                url,
                new JSONObject(params),
                response-> {
                    try {
                        Toast.makeText(context, response.getString("message"), Toast.LENGTH_SHORT).show();
                        ordersList.get(position).setServiceStatus("cancelled");
                        notifyDataSetChanged();

                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                },
                volleyError-> {
                    ErrorResponse e = new ErrorResponse(volleyError);
                    if (e.isCompleted()) {
                        Toast.makeText(context, "Status: " + e.getStatusCode(), Toast.LENGTH_SHORT).show();
                        JSONObject errors = e.getErrors();
                        Log.d("J_EXC_OR_", errors.toString());
                    }
                }) {
            @Override
            public Map<String, String> getHeaders() {
                HashMap<String, String> headers = new HashMap<>();
                headers.put("Authorization", "Bearer " + sharedPreference.getRememberToken(context));
                return headers;
            }
        };
        req.setRetryPolicy(new DefaultRetryPolicy(DefaultRetryPolicy.DEFAULT_TIMEOUT_MS * 1000, DefaultRetryPolicy.DEFAULT_MAX_RETRIES, DefaultRetryPolicy.DEFAULT_BACKOFF_MULT));
        ApplicationController.getInstance().addToRequestQueue(req);
    }

    String selectedMethod = "Cash";
    private void payForOrder(int position) {

        AlertDialog.Builder mBuilder = new AlertDialog.Builder(context);
        mBuilder.setTitle("Choose payment mode");

        String[] options = context.getResources().getStringArray(R.array.payment_method);

        mBuilder.setSingleChoiceItems(options, 0, (dialog, whichItem)-> selectedMethod = options[whichItem]);
        mBuilder.setPositiveButton("Next", (arg0, arg1)-> {

            HashMap<String, String> params = new HashMap<String, String>() {{
                    put("order_id", ordersList.get(position).getOrderNumber());
                    put("payment_method", selectedMethod.toLowerCase());
                }
            };
            Api.post(Utils.DOMAIN + "/api/v1/payments", params, new Api.DataListener() {
                @Override
                public void onData(String response, int resultCode) {
                    try {
                        JSONObject data = new JSONObject(response);
                        if (selectedMethod.equals("Cash")) {
                            AlertDialog.Builder alert = new AlertDialog.Builder(context);
                            alert.setCancelable(false);
                            alert.setMessage(data.getString("message"));
                            alert.setPositiveButton("OK", null);
                            alert.show();
                        } else {
                            Intent intent = new Intent(context, PaymentActivity.class);
                            intent.putExtra("order_id", ordersList.get(position).getOrderNumber());
                            context.startActivity(intent);
                        }

                    } catch (JSONException e) {
                        Toast.makeText(context, R.string.something_went_wrong, Toast.LENGTH_SHORT).show();
                    }
                }

                @Override
                public void onError(VolleyError volleyError, int resultCode) {
                    ErrorResponse e = new ErrorResponse(volleyError);
                    if (e.isCompleted()) {
                        Log.d("ERROR_PATMENT_", e.getErrors().toString());
                    }
                }

            }, Constants.PAYMENT_STATUS_REQUEST);

        });

        mBuilder.setNegativeButton("Cancel", null);
        AlertDialog alertDialog = mBuilder.create();
        alertDialog.show();
    }

    RatingBar ratingBar; EditText etReview;

    private void addRating(final int position) {
        AlertDialog.Builder dialog = new AlertDialog.Builder(context);
        View ratingLayout = ((MyBookingsActivity)context).getLayoutInflater().inflate(R.layout.view_order_rating, null);
        ratingBar = ratingLayout.findViewById(R.id.rating);
        etReview = ratingLayout.findViewById(R.id.write_a_review);
        dialog.setView(ratingLayout);
        dialog.setPositiveButton("Submit", (arg0, arg1)-> {
            postRatingReview(position);
        });
        dialog.setNegativeButton("Cancel", null);
        dialog.setCancelable(false);
        AlertDialog alertDialog = dialog.create();
        alertDialog.show();
    }

    private void postRatingReview(int position) {
        if (ratingBar.getRating() == 0) {
            Toast.makeText(context, "Rating can't be zero", Toast.LENGTH_SHORT).show();
            return;
        }
        final String url = Utils.DOMAIN + "/api/v1/rate-vendor/"+ ordersList.get(position).getOrderNumber();
        HashMap<String, String> params = new HashMap<>();
        params.put("rating", String.valueOf(ratingBar.getRating()));
        params.put("review", etReview.getText().toString());
        JsonObjectRequest req = new JsonObjectRequest(Request.Method.PUT, url, new JSONObject(params),
                response-> {
                    try {
                        Toast.makeText(context, response.getString("message"), Toast.LENGTH_SHORT).show();
                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                }, volleyError-> {
                    ErrorResponse e = new ErrorResponse(volleyError);
                    if (e.isCompleted()) {
                        Toast.makeText(context, "Status: " + e.getStatusCode(), Toast.LENGTH_SHORT).show();
                        JSONObject errors = e.getErrors();
                        Log.d("J_EXC_OR_", errors.toString());
                    }
                }) {
            @Override
            public Map<String, String> getHeaders() {
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
        return ordersList.size();
    }

    public static class ViewHolder extends RecyclerView.ViewHolder {
        public TextView serviceName, serviceDate, address, serviceStatus, cancelOrder, payAmount, rateNow;
        public ViewHolder(View itemView) {
            super(itemView);
            this.serviceName = itemView.findViewById(R.id.service_name);
            this.serviceDate = itemView.findViewById(R.id.service_date);
            this.address = itemView.findViewById(R.id.address);
            this.serviceStatus = itemView.findViewById(R.id.booking_status);
            this.cancelOrder = itemView.findViewById(R.id.cancel_booking);
            this.payAmount = itemView.findViewById(R.id.pay_for_booking);
            this.rateNow = itemView.findViewById(R.id.rate_now);
        }
    }
}