package com.wki.payservices.adapter;

import android.content.Context;
import android.content.Intent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.constraintlayout.widget.ConstraintLayout;
import androidx.viewpager.widget.PagerAdapter;
import com.squareup.picasso.Picasso;
import com.wki.payservices.R;
import com.wki.payservices.ServicesActivity;
import com.wki.payservices.Utils;
import com.wki.payservices.model.Service;

import java.util.List;

public class ViewPagerAdapter extends PagerAdapter {

    private Context mContext;
    private List<Service> mResources;

    public ViewPagerAdapter(Context mContext, List<Service> mResources) {
        this.mContext = mContext;
        this.mResources = mResources;
    }
    @Override
    public int getCount() {
        return mResources.size();
    }

    @Override
    public boolean isViewFromObject(View view, Object object) {
        return view == ((ConstraintLayout) object);
    }

    @Override
    public Object instantiateItem(ViewGroup container, int position) {
        View itemView = LayoutInflater.from(mContext).inflate(R.layout.pager_item, container, false);
        final ImageView imageView = itemView.findViewById(R.id.img_pager_item);
        String iconURL = Utils.CLOUDINARY + "/" + mResources.get(position).getIcon();
        final Service service = mResources.get(position);
        Picasso.get().load(iconURL).into(imageView);
        imageView.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent intent = new Intent(mContext, ServicesActivity.class);
                intent.putExtra("service_name", service.getName());
                intent.putExtra("service_id", service.getId());
                mContext.startActivity(intent);
            }
        });
        ((TextView)itemView.findViewById(R.id.title_service_name)).setText(mResources.get(position).getName());
        container.addView(itemView);
        return itemView;
    }
    @Override
    public void destroyItem(ViewGroup container, int position, Object object) {
        container.removeView((ConstraintLayout) object);
    }
}


