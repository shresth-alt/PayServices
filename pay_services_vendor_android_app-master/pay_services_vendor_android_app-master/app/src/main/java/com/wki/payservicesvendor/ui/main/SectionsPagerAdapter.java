package com.wki.payservicesvendor.ui.main;

import android.content.Context;

import androidx.annotation.Nullable;
import androidx.annotation.StringRes;
import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentManager;
import androidx.fragment.app.FragmentPagerAdapter;

import com.wki.payservicesvendor.R;

import org.jetbrains.annotations.NotNull;

/**
 * A [FragmentPagerAdapter] that returns a fragment corresponding to
 * one of the sections/tabs/pages.
 */
public class SectionsPagerAdapter extends FragmentPagerAdapter {

    public BookingFragment fragment1, fragment2, fragment3 = null;

    @StringRes
    private static final int[] TAB_TITLES = new int[] { R.string.tab_text_1, R.string.tab_text_2, R.string.tab_text_3 };
    private final Context mContext;

    public SectionsPagerAdapter(Context context, FragmentManager fm) {
        super(fm, BEHAVIOR_RESUME_ONLY_CURRENT_FRAGMENT);
        mContext = context;
    }

    @NotNull
    @Override
    public Fragment getItem(int position) {
        if (position == 0) {
            return fragment1 = new BookingFragment("pending");
        } else if (position == 1) {
            return fragment2 = new BookingFragment("accepted");
        } else {
            return fragment3 = new BookingFragment("completed");
        }
    }

    @Nullable
    @Override
    public CharSequence getPageTitle(int position) {
        return mContext.getResources().getString(TAB_TITLES[position]);
    }

    @Override
    public int getCount() {
        return 3;
    }
}