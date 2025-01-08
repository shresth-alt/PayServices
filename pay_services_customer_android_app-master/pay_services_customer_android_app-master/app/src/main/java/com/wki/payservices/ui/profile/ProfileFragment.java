package com.wki.payservices.ui.profile;

import android.content.Context;
import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;
import androidx.annotation.NonNull;
import androidx.fragment.app.Fragment;
import com.wki.payservices.AboutUsActivity;
import com.wki.payservices.ActionListener;
import com.wki.payservices.ContactUsActivity;
import com.wki.payservices.LoginActivity;
import com.wki.payservices.MyAddressesActivity;
import com.wki.payservices.MyBookingsActivity;
import com.wki.payservices.MyProfileActivity;
import com.wki.payservices.R;
import com.wki.payservices.SharedPreference;

public class ProfileFragment extends Fragment implements View.OnClickListener {

    TextView myProfile, myAddresses, myBookings, aboutUS, contactUS, rateUS, joinUS, logout, termsPolicies;

    private ActionListener listener;
    Context context;

    public View onCreateView(@NonNull LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        View root = inflater.inflate(R.layout.fragment_profile, container, false);
        myProfile = root.findViewById(R.id.my_profile);
        myAddresses = root.findViewById(R.id.my_addresses);
        myBookings = root.findViewById(R.id.my_bookings);
        aboutUS = root.findViewById(R.id.about_us);
        contactUS = root.findViewById(R.id.contact_us);
        rateUS = root.findViewById(R.id.rate_us);
        joinUS = root.findViewById(R.id.join_as_executive);
        termsPolicies = root.findViewById(R.id.terms_and_policies);
        logout = root.findViewById(R.id.logout);

        myProfile.setOnClickListener(this);
        myAddresses.setOnClickListener(this);
        myBookings.setOnClickListener(this);
        aboutUS.setOnClickListener(this);
        contactUS.setOnClickListener(this);
        rateUS.setOnClickListener(this);
        joinUS.setOnClickListener(this);
        logout.setOnClickListener(this);
        termsPolicies.setOnClickListener(this);
        return root;
    }

    @Override
    public void onAttach(Context context) {
        super.onAttach(context);
        try {
            listener = (ActionListener) context;
            this.context = context;
        } catch (ClassCastException castException) {
            /** The activity does not implement the listener. */
        }
    }


    @Override
    public void onClick(View v) {
        if (v == myProfile) {
            Intent intent = new Intent(context, MyProfileActivity.class);
            startActivity(intent);
        } else if (v == myAddresses) {
            Intent intent = new Intent(context, MyAddressesActivity.class);
            startActivity(intent);
        } else if (myBookings == v) {
            Intent intent = new Intent(context, MyBookingsActivity.class);
            startActivity(intent);
        } else if (aboutUS == v) {
            Intent intent = new Intent(context, AboutUsActivity.class);
            startActivity(intent);
        } else if (contactUS == v) {
            Intent intent = new Intent(context, ContactUsActivity.class);
            startActivity(intent);
        } else if (termsPolicies == v) {
            startActivity(new Intent(Intent.ACTION_VIEW,
                    Uri.parse("https://payservice.in/terms-policies/")));
        } else if (rateUS == v) {
            try {
                startActivity(new Intent(Intent.ACTION_VIEW, Uri.parse("market://details?id=com.wki.payservices")));
            } catch (android.content.ActivityNotFoundException anfe) {
                startActivity(new Intent(Intent.ACTION_VIEW,
                        Uri.parse("https://play.google.com/store/apps/details?id=com.wki.payservices")));
            }
        } else if (joinUS == v) {
            try {
                startActivity(new Intent(Intent.ACTION_VIEW, Uri.parse("market://details?id=com.wki.payservicesvendor")));
            } catch (android.content.ActivityNotFoundException anfe) {
                startActivity(new Intent(Intent.ACTION_VIEW,
                        Uri.parse("https://play.google.com/store/apps/details?id=com.wki.payservicesvendor")));
            }
        } else if (logout == v) {
            SharedPreference sharedPreference = new SharedPreference();
            sharedPreference.logoutUser(context);
            Intent intent = new Intent(context, LoginActivity.class);
            startActivity(intent);
            if (getActivity() != null) getActivity().finish();
        }
    }
}
