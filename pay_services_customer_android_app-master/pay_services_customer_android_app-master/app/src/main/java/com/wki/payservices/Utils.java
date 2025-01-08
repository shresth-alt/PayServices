package com.wki.payservices;

import android.content.Context;
import android.content.res.Resources;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;

public class Utils {

    public static final String CLOUDINARY = "https://res.cloudinary.com/pay-services/image/upload/";
    public static String VOLLEY_REQUESTS = "VOLLEY_REQUESTS";
//    public static String DOMAIN = "http://192.168.0.103:8000";
    public static String DOMAIN = "https://demo.payservice.in";
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

}
