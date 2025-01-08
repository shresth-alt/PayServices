package com.wki.payservicesvendor;

import android.app.AlertDialog;
import android.content.Context;
import android.content.res.Resources;
import android.graphics.drawable.ColorDrawable;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.TextView;

public class Utils {

    public static final String CLOUDINARY = "https://res.cloudinary.com/pay-services/image/upload/";
    public static String VOLLEY_REQUESTS = "VOLLEY_REQUESTS";

    public static String DOMAIN = "https://demo.payservice.in";
    public static String MEDIA_DOMAIN = DOMAIN + "/uploads/";
    public static String RAZORPAY_KEY = "rzp_test_b3bQamGyr089RL";
    public static String RAZORPAY_SECRET = "tK7eXd7M6e6U5Asom55drr05";

    /*
    public static String RAZORPAY_KEY = "rzp_live_UD1awHB547alFM";
    public static String RAZORPAY_SECRET = "WsnEP9pSTAYwc2ycIDSf8gab";
    */

    public static int dpToPx(int dp) {
        return (int) (dp * Resources.getSystem().getDisplayMetrics().density);
    }

    public static boolean isConnected(Context context) {
        ConnectivityManager connectivityMgr = (ConnectivityManager) context.getSystemService(Context.CONNECTIVITY_SERVICE);
        NetworkInfo activeNetwork = null;
        if (connectivityMgr != null) {
            activeNetwork = connectivityMgr.getActiveNetworkInfo();
        }
        return activeNetwork != null;
    }

    public static AlertDialog dialog;
    public static void showLoader(Context context, CharSequence charSequence) {
        AlertDialog.Builder builder = new AlertDialog.Builder(context);
        LayoutInflater inflater = LayoutInflater.from(context);
        View popUpView = inflater.inflate(R.layout.loader_layout, null);
        TextView loadingTextView = popUpView.findViewById(R.id.loading_text);
        loadingTextView.setText(charSequence);
        builder.setView(popUpView).setCancelable(false);

        dialog = builder.create();
        dialog.getWindow().setBackgroundDrawable(new ColorDrawable(android.graphics.Color.TRANSPARENT));
        dialog.show();
    }

    public static void hideLoader(Context context) {
        if (dialog != null && dialog.isShowing()) {
            dialog.dismiss();
        }
    }
}
