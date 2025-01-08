package com.wki.payservices;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.Dialog;
import android.content.Context;
import android.os.Bundle;
import android.util.Base64;
import android.util.Log;
import android.widget.Toast;
import androidx.appcompat.widget.Toolbar;
import com.android.volley.Request;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.StringRequest;
import com.razorpay.Checkout;
import com.razorpay.PaymentData;
import com.razorpay.PaymentResultWithDataListener;
import com.wki.payservices.databinding.ActivityPaymentBinding;
import com.wki.payservices.utility.Api;
import com.wki.payservices.utility.Constants;
import com.wki.payservices.utility.ErrorResponse;
import org.json.JSONException;
import org.json.JSONObject;
import java.util.HashMap;
import java.util.Locale;
import java.util.Map;
import io.github.inflationx.viewpump.ViewPumpContextWrapper;

public class PaymentActivity extends Activity implements PaymentResultWithDataListener {

    Checkout checkoutPay;
    ActivityPaymentBinding view;
    Dialog aDialog;
    String razorPayKey, razorPaySecret;
    String orderID;
    int amountPayble;
    SharedPreference sharedPreference = new SharedPreference();
    Toolbar toolbar;

    @Override
    protected void attachBaseContext(Context newBase) {
        super.attachBaseContext(ViewPumpContextWrapper.wrap(newBase));
    }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        view = ActivityPaymentBinding.inflate(getLayoutInflater());
        setContentView(view.getRoot());
        orderID = getIntent().getStringExtra("order_id");
        checkoutPay = new Checkout();
        razorPayKey = Utils.RAZORPAY_KEY;
        razorPaySecret = Utils.RAZORPAY_SECRET;
        checkoutPay.setKeyID(razorPayKey);
        Checkout.preload(this);
        String url = Utils.DOMAIN + "/api/v1/orders/" + orderID;
        Api.get(url, new Api.DataListener() {
            @Override
            public void onData(String response, int resultCode) {
                try {
                    JSONObject data = new JSONObject(response);
                    int id = data.getInt("id");
                    amountPayble = data.getInt("order_amount");
                    String serviceName = data.getString("service_name");
                    String serviceDate = data.getString("service_date");
                    view.orderId.setText("Order #" + id);
                    view.serviceName.setText(serviceName + " on " + serviceDate);
                    view.buttonPay.setText(String.format(Locale.getDefault(), "%s %s %d", "Pay", getString(R.string.rs_icon), amountPayble));
                    view.buttonPay.setOnClickListener(v-> createOrder());
                } catch (JSONException e) {
                    e.printStackTrace();
                    Toast.makeText(PaymentActivity.this, e.toString(), Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onError(VolleyError volleyError, int resultCode) {

                ErrorResponse e = new ErrorResponse(volleyError);
                if (e.isCompleted()) {
                    Log.d("DATA_RES_", e.getErrors().toString());

                }
            }

        }, Constants.GET_ORDER_REQUEST);
    }

    private void createOrder() {
        AlertDialog.Builder builder = new AlertDialog.Builder(this);
        builder.setMessage("Redirecting...");
        aDialog = builder.create();
        aDialog.show();
        String url = "https://api.razorpay.com/v1/orders";
        StringRequest strReq = new StringRequest(
                Request.Method.POST,
                url,
                response-> {
                    Log.d("ORDER_RES_", response);

                    try {
                        JSONObject data = new JSONObject(response);

                        String rpOrderID = data.getString("id");

                        //Start payment
                        JSONObject options = new JSONObject();
                        options.put("name", "Pay Services");
                        options.put("description", "Order ID #" + orderID);
                        options.put("image", Utils.DOMAIN + "/pay-logo.png");
                        options.put("order_id", rpOrderID);
                        options.put("theme.color", "#1474a4");
                        options.put("currency", "INR"); // Currency = rupees
                        options.put("amount", String.valueOf(amountPayble*100)); //Amount is in paisa (rupees*100)

                        JSONObject preFill = new JSONObject();
                        preFill.put("email", "support@payservice.in");
//                        preFill.put("contact", SharedPreference.getPref(this, "contact"));
                        preFill.put("contact", "1231231230");

                        options.put("prefill", preFill);

                        //Open checkout form of razor pay
                        checkoutPay.open(PaymentActivity.this, options);

                    } catch (JSONException e) {
                        e.printStackTrace();
                        Toast.makeText(PaymentActivity.this, R.string.something_went_wrong, Toast.LENGTH_SHORT).show();
                    }
                },
                volleyError-> {

                    aDialog.dismiss();

                    ErrorResponse e = new ErrorResponse(volleyError);
                    if (e.isCompleted()) {
                        Toast.makeText(PaymentActivity.this, "Status: " + e.getStatusCode(), Toast.LENGTH_SHORT).show();
                        JSONObject errors = e.getErrors();
                        Log.d("J_EXC_OR_", errors.toString());
                    }
                }
        ) {
            @Override
            protected Map<String, String> getParams() {
                HashMap<String, String> params = new HashMap<>();
                params.put("amount", String.valueOf(amountPayble*100));
                params.put("currency", "INR");
                return params;
            }

            @Override
            public Map<String, String> getHeaders() {
                HashMap<String, String> headers = new HashMap<>();
                String encodedCredentials = Base64.encodeToString((razorPayKey + ":" + razorPaySecret).getBytes(), Base64.NO_WRAP);
                headers.put("Authorization", "Basic " + encodedCredentials);
                return headers;
            }
        };

        ApplicationController.getInstance().addToRequestQueue(strReq, Utils.VOLLEY_REQUESTS);
    }

    @Override
    public void onPaymentSuccess(String s, PaymentData paymentData) {
        aDialog.dismiss();

        HashMap<String, String> params = new HashMap<String, String>() {
            {
                put("order_id", orderID);
                put("razorpay_payment_id", paymentData.getPaymentId());
                put("razorpay_order_id", paymentData.getOrderId());
                put("razorpay_signature", paymentData.getSignature());
            }
        };

        saveToServer(params);
    }

    @Override
    public void onPaymentError(int i, String s, PaymentData paymentData) {
        Log.d("ERROR_PAYMENT_", paymentData.getData().toString());
        aDialog.dismiss();
        Toast.makeText(this, R.string.something_went_wrong, Toast.LENGTH_SHORT).show();
    }

    private void saveToServer(HashMap<String, String> params) {

        String url = Utils.DOMAIN + "/api/v1/process-online-payment";

        Api.post(url, params, new Api.DataListener() {

            @Override
            public void onData(String response, int resultCode) {
                Toast.makeText(PaymentActivity.this, "Payment success", Toast.LENGTH_SHORT).show();
                finish();
            }

            @Override
            public void onError(VolleyError volleyError, int resultCode) {
                Toast.makeText(PaymentActivity.this, R.string.something_went_wrong, Toast.LENGTH_SHORT).show();
            }

        }, Constants.PAY_SUCCESS_REQUEST);
    }
}
