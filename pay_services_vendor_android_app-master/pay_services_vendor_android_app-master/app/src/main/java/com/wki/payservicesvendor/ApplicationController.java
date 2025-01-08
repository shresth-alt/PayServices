package com.wki.payservicesvendor;

import android.app.Application;
import android.text.TextUtils;

import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.VolleyLog;
import com.android.volley.toolbox.Volley;

import io.github.inflationx.calligraphy3.CalligraphyConfig;
import io.github.inflationx.calligraphy3.CalligraphyInterceptor;
import io.github.inflationx.viewpump.ViewPump;


public class ApplicationController extends Application {

  public static final String TAG = "VolleyPatterns";
  private RequestQueue mRequestQueue;
  private static ApplicationController sInstance;

  @Override
  public void onCreate() {
    super.onCreate();
    sInstance = this;
    String fonts = "fonts/product_sans_regular.ttf";
    ViewPump.init(ViewPump.builder().addInterceptor(new CalligraphyInterceptor(new CalligraphyConfig.Builder().setDefaultFontPath(fonts).setFontAttrId(R.attr.fontPath).build())).build());
  }

  public static synchronized ApplicationController getInstance() {
    return sInstance;
  }

  public RequestQueue getRequestQueue() {
    if (mRequestQueue == null) {
      mRequestQueue = Volley.newRequestQueue(getApplicationContext());
    }
    return mRequestQueue;
  }

  public <T> void addToRequestQueue(Request<T> req, String tag) {
    req.setTag(TextUtils.isEmpty(tag) ? TAG : tag);

    VolleyLog.d("Adding request to queue: %s", req.getUrl());

    getRequestQueue().add(req);
  }

  public <T> void addToRequestQueue(Request<T> req) {
    req.setTag(TAG);
    getRequestQueue().add(req);
  }

  public void cancelPendingRequests(Object tag) {
    if (mRequestQueue != null) {
      mRequestQueue.cancelAll(tag);
    }
  }
}
