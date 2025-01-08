package com.wki.payservices;

import android.content.Context;
import android.content.SharedPreferences;
import android.content.SharedPreferences.Editor;

public class SharedPreference {

  private static final String PREFS_NAME = "PAY_SERVICES_APP";
  private static final String CART_PRODUCTS = "CART_PRODUCTS";
  private static final String REMEMBER_TOKEN = "REMEMBER_TOKEN";
  private static final String FCM_TOKEN = "FCM_TOKEN";
  private static final String FCM_UPDATED_ON_SERVER = "FCM_UPDATED_ON_SERVER";
  private static final String LOGGED_IN = "LOGGED_IN";
  private static final String USER_ID = "USER_ID";

  /* FCM */

  public void setFCMToken(Context context, String fcmToken) {
    SharedPreferences settings;
    settings = context.getSharedPreferences(PREFS_NAME, Context.MODE_PRIVATE);
    Editor editor = settings.edit();
    editor.putString(FCM_TOKEN, fcmToken);
    editor.apply();
  }

  public String getFCMToken(Context context) {
    SharedPreferences settings = context.getSharedPreferences(PREFS_NAME, Context.MODE_PRIVATE);
    return settings.getString(FCM_TOKEN, "");
  }

  public void setRememberToken(Context context, String rememberToken) {
    SharedPreferences settings;
    settings = context.getSharedPreferences(PREFS_NAME, Context.MODE_PRIVATE);
    SharedPreferences.Editor editor = settings.edit();
    editor.putString(REMEMBER_TOKEN, rememberToken);
    editor.apply();
  }

  public String getRememberToken(Context context) {
    SharedPreferences settings;
    settings = context.getSharedPreferences(PREFS_NAME, Context.MODE_PRIVATE);
    return settings.getString(REMEMBER_TOKEN, "");
  }

  public void setFcmUpdatedOnServer(Context context, boolean isUpdatedOnServer) {
    SharedPreferences settings;
    settings = context.getSharedPreferences(PREFS_NAME, Context.MODE_PRIVATE);
    Editor editor = settings.edit();
    editor.putBoolean(FCM_UPDATED_ON_SERVER, isUpdatedOnServer);
    editor.apply();
  }

  public boolean getFcmUpdatedOnServer(Context context) {
    SharedPreferences settings = context.getSharedPreferences(PREFS_NAME, Context.MODE_PRIVATE);
    return settings.getBoolean(FCM_UPDATED_ON_SERVER, false);
  }

  public void setLoggedIn(Context context, boolean loggedIn) {
    SharedPreferences settings;
    settings = context.getSharedPreferences(PREFS_NAME, Context.MODE_PRIVATE);
    Editor editor = settings.edit();
    editor.putBoolean(LOGGED_IN, loggedIn);
    editor.apply();
  }

  public void logoutUser(Context context) {
    SharedPreferences settings;
    settings = context.getSharedPreferences(PREFS_NAME, Context.MODE_PRIVATE);
    if(settings.contains(LOGGED_IN)) {
      Editor editor = settings.edit();
      editor.remove(LOGGED_IN);
      editor.remove("address");
      editor.remove("city");
      editor.remove("name");
      editor.remove("USER_ID");
      editor.remove("state");
      editor.remove("REMEMBER_TOKEN");
      editor.remove("postal_code");
      editor.remove("LOGGED_IN");
      editor.apply();
    }
  }

  public boolean getLoggedIn(Context context) {
    SharedPreferences settings = context.getSharedPreferences(PREFS_NAME, Context.MODE_PRIVATE);
    return settings.getBoolean(LOGGED_IN, false);
  }

  public void setUserId(Context context, int userId) {
    SharedPreferences settings;
    settings = context.getSharedPreferences(PREFS_NAME, Context.MODE_PRIVATE);
    Editor editor = settings.edit();
    editor.putInt(USER_ID, userId);
    editor.apply();
  }

  public static void setPref(Context context, String key, String value) {
    SharedPreferences settings;
    settings = context.getSharedPreferences(PREFS_NAME, Context.MODE_PRIVATE);
    Editor editor = settings.edit();
    editor.putString(key, value);
    editor.apply();
  }

  public static String getPref(Context context, String key) {
    SharedPreferences settings;
    settings = context.getSharedPreferences(PREFS_NAME, Context.MODE_PRIVATE);
    return settings.getString(key, "");
  }

  public static void deletePref(Context context, String key) {
    SharedPreferences settings;
    settings = context.getSharedPreferences(PREFS_NAME, Context.MODE_PRIVATE);
    Editor editor = settings.edit();
    editor.remove(key);
    editor.apply();
  }

  public int getUserId(Context context) {
    if(getLoggedIn(context)) {
      SharedPreferences settings = context.getSharedPreferences(PREFS_NAME, Context.MODE_PRIVATE);
      return settings.getInt(USER_ID, 0);
    }
    else {
      return 0;
    }
  }

}
