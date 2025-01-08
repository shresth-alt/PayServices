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
import com.wki.payservicesvendor.model.VendorPayment;
import com.wki.payservicesvendor.ui.main.BookingFragment;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

public class VendorPaymentAdapter extends RecyclerView.Adapter<VendorPaymentAdapter.ViewHolder>{

    private List<VendorPayment> statementsList;
    Context context;

    public VendorPaymentAdapter(List<VendorPayment> statementsList, Context context) {
        this.statementsList = statementsList;
        this.context = context;
    }

    @Override
    public ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        LayoutInflater layoutInflater = LayoutInflater.from(parent.getContext());
        View listItem = layoutInflater.inflate(R.layout.account_statement_item, parent, false);
        return new ViewHolder(listItem);
    }

    @Override
    public void onBindViewHolder(ViewHolder holder, final int position) {
        final VendorPayment vendorPayment = statementsList.get(position);
        if (vendorPayment.getPaymentType().equals("Debit")) {
            holder.orderNumber.setText(vendorPayment.getOrderId());
            holder.orderDate.setText(vendorPayment.getTransactionDate());
            String amount = context.getString(R.string.label_rs_sym) + vendorPayment.getOrderAmount();
            holder.orderAmount.setText(amount);
            holder.paymentType.setText(vendorPayment.getPaymentType());
            amount = context.getString(R.string.label_rs_sym) + vendorPayment.getCommissionAmount();
            holder.transactionAmount.setText(amount);
            amount = context.getString(R.string.label_rs_sym) + vendorPayment.getServiceCharge();
            holder.serviceCharge.setText(amount);
            amount = context.getString(R.string.label_rs_sym) + (vendorPayment.getCommissionAmount() - vendorPayment.getCommissionGST());
            holder.convenienceFee.setText(amount);
            amount = context.getString(R.string.label_rs_sym) + vendorPayment.getCommissionGST();
            holder.gstAmount.setText(amount);
            holder.orderNumberWrapper.setVisibility(View.VISIBLE);
            holder.orderAmountWrapper.setVisibility(View.VISIBLE);
            holder.serviceChargeWrapper.setVisibility(View.VISIBLE);
            holder.convenienceFeeWrapper.setVisibility(View.VISIBLE);
            holder.gstWrapper.setVisibility(View.VISIBLE);
            holder.labelAmountType.setText(context.getString(R.string.amount));
            holder.labelDateType.setText(context.getString(R.string.label_order_date));
        } else {
            holder.labelAmountType.setText(context.getString(R.string.credit_amount));
            holder.labelDateType.setText(context.getString(R.string.label_credit_date));
            holder.orderNumberWrapper.setVisibility(View.GONE);
            holder.orderAmountWrapper.setVisibility(View.GONE);
            holder.serviceChargeWrapper.setVisibility(View.GONE);
            holder.convenienceFeeWrapper.setVisibility(View.GONE);
            holder.gstWrapper.setVisibility(View.GONE);
            holder.orderDate.setText(vendorPayment.getTransactionDate());
            holder.paymentType.setText(vendorPayment.getPaymentType());
            String amount = context.getString(R.string.label_rs_sym) + vendorPayment.getCommissionAmount();
            holder.transactionAmount.setText(amount);
        }
    }

    @Override
    public int getItemCount() {
        return statementsList.size();
    }

    public static class ViewHolder extends RecyclerView.ViewHolder {
        public TextView orderNumber, orderDate, orderAmount, paymentType, transactionAmount, serviceCharge, convenienceFee, gstAmount, labelAmountType, labelDateType;
        LinearLayout orderNumberWrapper, orderDateWrapper, orderAmountWrapper, serviceChargeWrapper, convenienceFeeWrapper, gstWrapper;
        public ViewHolder(View itemView) {
            super(itemView);
            this.orderNumber = itemView.findViewById(R.id.order_number);
            this.orderDate = itemView.findViewById(R.id.order_date);
            this.orderAmount = itemView.findViewById(R.id.order_amount);
            this.serviceCharge = itemView.findViewById(R.id.service_charge);
            this.paymentType = itemView.findViewById(R.id.pay_type);
            this.convenienceFee = itemView.findViewById(R.id.convenience_fee);
            this.transactionAmount = itemView.findViewById(R.id.transaction_amount);
            this.orderNumberWrapper = itemView.findViewById(R.id.order_number_wrapper);
            this.orderDateWrapper = itemView.findViewById(R.id.order_date_wrapper);
            this.orderAmountWrapper = itemView.findViewById(R.id.order_amount_wrapper);
            this.gstAmount = itemView.findViewById(R.id.gst);
            this.serviceChargeWrapper = itemView.findViewById(R.id.service_charge_wrapper);
            this.convenienceFeeWrapper = itemView.findViewById(R.id.convenience_fee_wrapper);
            this.gstWrapper = itemView.findViewById(R.id.gst_wrapper);
            this.labelAmountType = itemView.findViewById(R.id.label_amount_type);
            this.labelDateType = itemView.findViewById(R.id.date_type);
        }
    }
}