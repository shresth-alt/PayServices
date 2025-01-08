package com.wki.payservicesvendor;

import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.Typeface;
import android.net.Uri;
import android.os.Bundle;
import com.android.volley.AuthFailureError;
import com.android.volley.DefaultRetryPolicy;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.JsonObjectRequest;
import com.google.android.material.tabs.TabLayout;
import androidx.annotation.NonNull;
import androidx.appcompat.app.AlertDialog;
import androidx.appcompat.widget.Toolbar;
import androidx.viewpager.widget.ViewPager;
import androidx.appcompat.app.AppCompatActivity;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;
import com.google.android.play.core.appupdate.AppUpdateInfo;
import com.google.android.play.core.appupdate.AppUpdateManager;
import com.google.android.play.core.appupdate.AppUpdateManagerFactory;
import com.google.android.play.core.install.model.AppUpdateType;
import com.google.android.play.core.install.model.UpdateAvailability;
import com.google.android.play.core.tasks.OnSuccessListener;
import com.google.android.play.core.tasks.Task;
import com.wki.payservicesvendor.adapter.OrdersAdapter;
import com.wki.payservicesvendor.model.Order;
import com.wki.payservicesvendor.ui.main.SectionsPagerAdapter;
import org.json.JSONObject;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import io.github.inflationx.viewpump.ViewPumpContextWrapper;

public class OrdersActivity extends AppCompatActivity implements ActionListener {

    private Toolbar toolbar;
    private List<Order> bookingList = new ArrayList<>();
    private SharedPreference sharedPreference = new SharedPreference();
    OrdersAdapter ordersAdapter;

    SectionsPagerAdapter sectionsPagerAdapter;
    ViewPager viewPager;

    @Override
    protected void attachBaseContext(Context newBase) {
        super.attachBaseContext(ViewPumpContextWrapper.wrap(newBase));
    }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_orders);
        toolbar = findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);
        if (getSupportActionBar() != null) {
            getSupportActionBar().setTitle("Vendor Bookings");
        }
        sectionsPagerAdapter = new SectionsPagerAdapter(this, getSupportFragmentManager());
        viewPager = findViewById(R.id.view_pager);
        viewPager.setAdapter(sectionsPagerAdapter);
        viewPager.setOffscreenPageLimit(0);
        TabLayout tabLayout = findViewById(R.id.tabs);
        tabLayout.setupWithViewPager(viewPager);
        Typeface tf = Typeface.createFromAsset(getAssets(), "fonts/product_sans_regular.ttf");
        ViewGroup vg = (ViewGroup) tabLayout.getChildAt(0);
        int tabsCount = vg.getChildCount();
        for (int j = 0; j < tabsCount; j++) {
            ViewGroup vgTab = (ViewGroup) vg.getChildAt(j);
            int tabChildsCount = vgTab.getChildCount();
            for (int i = 0; i < tabChildsCount; i++) {
                View tabViewChild = vgTab.getChildAt(i);
                if (tabViewChild instanceof TextView) {
                    ((TextView) tabViewChild).setTypeface(tf);
                    ((TextView) tabViewChild).setTransformationMethod(null);
                }
            }
        }
        sendTokenOnServer();
        checkAppUpdate();
    }

    AppUpdateManager appUpdateManager;

    private void checkAppUpdate() {
        appUpdateManager = AppUpdateManagerFactory.create(OrdersActivity.this);
        Task<AppUpdateInfo> appUpdateInfoTask = appUpdateManager.getAppUpdateInfo();
        appUpdateInfoTask.addOnSuccessListener(new OnSuccessListener<AppUpdateInfo>() {
            @Override
            public void onSuccess(AppUpdateInfo appUpdateInfo) {
                if (appUpdateInfo.updateAvailability() == UpdateAvailability.UPDATE_AVAILABLE
                        && appUpdateInfo.isUpdateTypeAllowed(AppUpdateType.IMMEDIATE)) {
                    showUpdateAlert();
                }
            }
        });
    }

    private void showUpdateAlert() {
        AlertDialog.Builder alertDialog = new AlertDialog.Builder(OrdersActivity.this);
        alertDialog.setMessage("A newer version of this app is available. Please update your app!");
        alertDialog.setCancelable(false);
        alertDialog.setNegativeButton("Cancel", null);
        alertDialog.setPositiveButton("Open Play Store", new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int which) {
                final String appPackageName = getPackageName();
                try {
                    startActivity(new Intent(Intent.ACTION_VIEW, Uri.parse("market://details?id=" + appPackageName)));
                } catch (android.content.ActivityNotFoundException e) {
                    startActivity(new Intent(Intent.ACTION_VIEW, Uri.parse("https://play.google.com/store/apps/details?id=" + appPackageName)));
                }
            }
        });
        alertDialog.show();
    }

    private void sendTokenOnServer() {
        if (Utils.isConnected(OrdersActivity.this)) {
            final String URL = Utils.DOMAIN + "/api/v1/update-user-token";
            HashMap<String, String> params = new HashMap<>();
            params.put("token", String.valueOf(sharedPreference.getFCMToken(OrdersActivity.this)));
            JsonObjectRequest req = new JsonObjectRequest(URL, new JSONObject(params),
                    new Response.Listener<JSONObject>() {
                        @Override
                        public void onResponse(JSONObject response) {
                            sharedPreference.setFcmUpdatedOnServer(OrdersActivity.this, true);
                        }
                    }, new Response.ErrorListener() {
                @Override
                public void onErrorResponse(VolleyError error) {

                }
            }){
                @Override
                public Map<String, String> getHeaders() throws AuthFailureError {
                    HashMap<String, String> headers = new HashMap<>();
                    headers.put("Authorization", "Bearer " + sharedPreference.getRememberToken(OrdersActivity.this));
                    return headers;
                }
            };
            req.setRetryPolicy(new DefaultRetryPolicy(DefaultRetryPolicy.DEFAULT_TIMEOUT_MS * 1000, DefaultRetryPolicy.DEFAULT_MAX_RETRIES, DefaultRetryPolicy.DEFAULT_BACKOFF_MULT));
            ApplicationController.getInstance().addToRequestQueue(req);
        }
    }

    @Override
    protected void onResume() {
        super.onResume();
        refreshAll();
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        getMenuInflater().inflate(R.menu.booking_sc_menu, menu);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(@NonNull MenuItem item) {
        int id = item.getItemId();
        if (id == android.R.id.home) {
            finish();
        } else if (id == R.id.action_refresh) {
            refreshList();
        } else if (id == R.id.action_my_profile) {
            Intent i = new Intent(OrdersActivity.this, MyProfileActivity.class);
            startActivity(i);
        } else if (id == R.id.action_account_statements) {
            Intent i = new Intent(OrdersActivity.this, AccountStatements.class);
            startActivity(i);
        } else if (id == R.id.action_logout) {
            sharedPreference.logoutUser(this);
            Intent intent = new Intent(this, LoginActivity.class);
            startActivity(intent);
            finishAffinity();
        }
        return super.onOptionsItemSelected(item);
    }

    @Override
    public void refreshList() {
        if (viewPager.getCurrentItem() == 0) {
            sectionsPagerAdapter.fragment1.loadBookings(true);
        } else if (viewPager.getCurrentItem() == 1) {
            sectionsPagerAdapter.fragment2.loadBookings(true);
        } else {
            sectionsPagerAdapter.fragment3.loadBookings(true);
        }
    }

    @Override
    public void refreshAll() {
        if (sectionsPagerAdapter.fragment1 != null) sectionsPagerAdapter.fragment1.loadBookings(true);
        if (sectionsPagerAdapter.fragment2 != null) sectionsPagerAdapter.fragment2.loadBookings(true);
        if (sectionsPagerAdapter.fragment3 != null) sectionsPagerAdapter.fragment3.loadBookings(true);
    }
}