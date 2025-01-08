package com.wki.payservicesvendor.adapter;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;
import androidx.appcompat.app.AlertDialog;
import androidx.recyclerview.widget.RecyclerView;

import com.android.volley.DefaultRetryPolicy;
import com.android.volley.NetworkResponse;
import com.android.volley.Request;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.JsonObjectRequest;
import com.wki.payservicesvendor.ApplicationController;
import com.wki.payservicesvendor.CompleteOrderActivity;
import com.wki.payservicesvendor.OrdersActivity;
import com.wki.payservicesvendor.R;
import com.wki.payservicesvendor.SharedPreference;
import com.wki.payservicesvendor.Utils;
import com.wki.payservicesvendor.model.Order;
import com.wki.payservicesvendor.ui.main.BookingFragment;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

public class OrdersAdapter extends RecyclerView.Adapter<OrdersAdapter.ViewHolder>{

    private List<Order> ordersList;
    Context context;
    SharedPreference sharedPreference = new SharedPreference();
    BookingFragment placeholderFragment;

    public OrdersAdapter(List<Order> serviceList, Context context, BookingFragment placeholderFragment) {
        this.ordersList = serviceList;
        this.context = context;
        this.placeholderFragment = placeholderFragment;
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
        String customerNameMobile = "+91 " + service.getCustomerMobile();
        holder.customerDetail.setText(customerNameMobile);
        switch (service.getServiceStatus()) {
            case "pending":
                holder.colorIndicator.setBackgroundColor(context.getResources().getColor(R.color.statusPending));
                holder.acceptOrder.setVisibility(View.VISIBLE);
                holder.completeOrder.setVisibility(View.GONE);
                holder.markPayment.setVisibility(View.GONE);
                holder.orderComment.setVisibility(View.GONE);
                break;
            case "accepted":
                holder.acceptOrder.setVisibility(View.GONE);
                holder.markPayment.setVisibility(View.GONE);
                holder.completeOrder.setVisibility(View.VISIBLE);
                holder.colorIndicator.setBackgroundColor(context.getResources().getColor(R.color.completeOrder));
                holder.orderComment.setVisibility(View.VISIBLE);
                break;
            case "completed":
                if (service.getPaymentStatus().equals("pending") && service.getPaymentMethod().equals("cash")) {
                    holder.markPayment.setVisibility(View.VISIBLE);
                    holder.completeOrder.setVisibility(View.GONE);
                    holder.acceptOrder.setVisibility(View.GONE);
                } else {
                    holder.actionButtonsWrapper.setVisibility(View.GONE);
                    holder.colorIndicator.setBackgroundColor(context.getResources().getColor(R.color.statusCompleted));
                }
                holder.orderComment.setVisibility(View.GONE);
                break;
            default:
                holder.actionButtonsWrapper.setVisibility(View.GONE);
                holder.orderComment.setVisibility(View.GONE);
                holder.colorIndicator.setBackgroundColor(context.getResources().getColor(R.color.statusCompleted));
                break;
        }
        holder.acceptOrder.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                acceptOrderConfirmation(position);
            }
        });
        holder.acceptOrder.setTransformationMethod(null);
        holder.completeOrder.setTransformationMethod(null);
        holder.orderComment.setTransformationMethod(null);
        holder.completeOrder.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Order order = ordersList.get(position);
                Intent intent = new Intent(context, CompleteOrderActivity.class);
                intent.putExtra("order_id", order.getOrderNumber());
                context.startActivity(intent);
            }
        });
        holder.orderComment.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                openOrderCommentDialog(position);
            }
        });
        holder.markPayment.setTransformationMethod(null);
        holder.markPayment.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if (Utils.isConnected(context)) {
                    AlertDialog.Builder alertDialog = new AlertDialog.Builder(context);
                    alertDialog.setMessage("Mark payment received?");
                    alertDialog.setCancelable(false);
                    alertDialog.setNegativeButton("No", null);
                    alertDialog.setPositiveButton("Yes", new DialogInterface.OnClickListener() {
                        @Override
                        public void onClick(DialogInterface dialog, int which) {
                            markPayment(position);
                        }
                    });
                    alertDialog.show();
                } else {
                    Toast.makeText(context, "No internet connection", Toast.LENGTH_SHORT).show();
                }
            }
        });
    }

    EditText orderComment;

    private void openOrderCommentDialog(final int orderId) {
        AlertDialog.Builder mBuilder = new AlertDialog.Builder(context);
        mBuilder.setTitle("Enter order status");
        final View customLayout = ((OrdersActivity)context).getLayoutInflater().inflate(R.layout.order_comment_popup, null);
        orderComment = customLayout.findViewById(R.id.cancellation_reason);
        orderComment.setText(ordersList.get(orderId).getOrderComments());
        mBuilder.setView(customLayout);
        mBuilder.setPositiveButton("Submit", new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int which) {
                if (!orderComment.getText().toString().trim().isEmpty()) {
                    updateOrderComment(ordersList.get(orderId).getOrderNumber());
                } else {
                    Toast.makeText(context, "Please enter a valid reason behind delay in booking completion", Toast.LENGTH_SHORT).show();
                }
            }
        });
        mBuilder.setNegativeButton("Cancel", null);
        AlertDialog alertDialog = mBuilder.create();
        alertDialog.show();
        alertDialog.getButton(DialogInterface.BUTTON_POSITIVE).setTextSize(16);
        alertDialog.getButton(DialogInterface.BUTTON_NEGATIVE).setTextSize(16);
        alertDialog.getButton(DialogInterface.BUTTON_POSITIVE).setAllCaps(false);
        alertDialog.getButton(DialogInterface.BUTTON_NEGATIVE).setAllCaps(false);
    }

    private void updateOrderComment(String orderId) {
        String url = Utils.DOMAIN + "/api/v1/update-order-comment/" + orderId;
        HashMap<String, String> params = new HashMap<String, String>() {{
            put("comments", orderComment.getText().toString().trim());
        }};
        JsonObjectRequest req = new JsonObjectRequest(Request.Method.PUT, url, new JSONObject(params),
                new Response.Listener<JSONObject>() {
                    @Override
                    public void onResponse(JSONObject response) {
                        try {
                            Toast.makeText(context, response.getString("message"), Toast.LENGTH_SHORT).show();
                            placeholderFragment.listener.refreshAll();
                        } catch (JSONException e) {
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
                        JSONArray errors = errorObject.getJSONArray("errors");
                        Toast.makeText(context, errors.getString(0), Toast.LENGTH_LONG).show();
                    } catch (JSONException e) {
                        Toast.makeText(context, "Something went wrong on server", Toast.LENGTH_SHORT).show();
                        e.printStackTrace();
                    }
                }
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

    private void markPayment(final int position) {
        String url = Utils.DOMAIN + "/api/v1/process-cash-payment";
        HashMap<String, String> params = new HashMap<String, String>() {{
            put("order_id", ordersList.get(position).getOrderNumber());
        }};
        JsonObjectRequest req = new JsonObjectRequest(Request.Method.POST, url, new JSONObject(params),
                new Response.Listener<JSONObject>() {
                    @Override
                    public void onResponse(JSONObject response) {
                        try {
                            Toast.makeText(context, response.getString("message"), Toast.LENGTH_SHORT).show();
                            placeholderFragment.listener.refreshAll();
                        } catch (JSONException e) {
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
                        JSONArray errors = errorObject.getJSONArray("errors");
                        Toast.makeText(context, errors.getString(0), Toast.LENGTH_LONG).show();
                    } catch (JSONException e) {
                        Toast.makeText(context, "Something went wrong on server", Toast.LENGTH_SHORT).show();
                        e.printStackTrace();
                    }
                }
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

    private void acceptOrderConfirmation(final int position) {
        AlertDialog.Builder mBuilder = new AlertDialog.Builder(context);
        mBuilder.setTitle("Are you sure you want to accept booking?");
        mBuilder.setPositiveButton("yes", new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface arg0, int arg1) {
                acceptOrder(position);
            }
        });
        mBuilder.setNegativeButton("no", null);
        AlertDialog alertDialog = mBuilder.create();
        alertDialog.show();
    }

    private void acceptOrder(final int position) {
        final String URL = Utils.DOMAIN + "/api/v1/accept-order";
        HashMap<String, String> params = new HashMap<>();
        Toast.makeText(context, "Accepting Booking...", Toast.LENGTH_SHORT).show();
        JsonObjectRequest req = new JsonObjectRequest(Request.Method.PUT, URL + "/" + ordersList.get(position).getOrderNumber(), new JSONObject(params),
                new Response.Listener<JSONObject>() {
                    @Override
                    public void onResponse(JSONObject response) {
                        try {
                            Toast.makeText(context, response.getString("message"), Toast.LENGTH_SHORT).show();
                            ordersList.get(position).setServiceStatus("accepted");
                            placeholderFragment.listener.refreshAll();
                        } catch (JSONException e) {
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
                        JSONArray errors = errorObject.getJSONArray("errors");
                        Toast.makeText(context, errors.getString(0), Toast.LENGTH_LONG).show();
                    } catch (JSONException e) {
                        Toast.makeText(context, "Something went wrong on server", Toast.LENGTH_SHORT).show();
                        e.printStackTrace();
                    }
                }
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
        public TextView serviceName, serviceDate, address, serviceStatus, customerDetail;
        Button acceptOrder, completeOrder, markPayment, orderComment;
        public View colorIndicator;
        LinearLayout actionButtonsWrapper;
        public ViewHolder(View itemView) {
            super(itemView);
            this.serviceName = itemView.findViewById(R.id.service_name);
            this.serviceDate = itemView.findViewById(R.id.service_date);
            this.address = itemView.findViewById(R.id.address);
            this.serviceStatus = itemView.findViewById(R.id.booking_status);
            this.colorIndicator = itemView.findViewById(R.id.color_indicator);
            this.acceptOrder = itemView.findViewById(R.id.accept_order);
            this.actionButtonsWrapper = itemView.findViewById(R.id.action_buttons);
            this.completeOrder = itemView.findViewById(R.id.complete_order);
            this.markPayment = itemView.findViewById(R.id.mark_payment);
            this.customerDetail = itemView.findViewById(R.id.customer_name_mobile);
            this.orderComment = itemView.findViewById(R.id.comment_order);
        }
    }
}