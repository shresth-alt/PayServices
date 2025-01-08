package com.wki.payservices.adapter;

import android.content.Context;
import android.content.Intent;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import androidx.recyclerview.widget.RecyclerView;

import com.squareup.picasso.Picasso;
import com.wki.payservices.R;
import com.wki.payservices.ServicesActivity;
import com.wki.payservices.Utils;
import com.wki.payservices.model.Service;

import java.util.List;

public class ServicesAdapter extends RecyclerView.Adapter<ServicesAdapter.ViewHolder>{

    private List<Service> serviceList;
    Context context;

    public ServicesAdapter(List<Service> serviceList, Context context) {
        this.serviceList = serviceList;
        this.context = context;
    }

    @Override
    public ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        LayoutInflater layoutInflater = LayoutInflater.from(parent.getContext());
        View listItem= layoutInflater.inflate(R.layout.service_item, parent, false);
        return new ViewHolder(listItem);
    }

    @Override
    public void onBindViewHolder(ViewHolder holder, int position) {
        final Service service = serviceList.get(position);
        holder.serviceName.setText(service.getName());
        String iconURL = Utils.CLOUDINARY + "w_" + Utils.dpToPx(50) + "/" + serviceList.get(position).getIcon();
        Picasso.get().load(iconURL).into(holder.serviceIcon);
        holder.itemView.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent intent = new Intent(context, ServicesActivity.class);
                intent.putExtra("service_name", service.getName());
                intent.putExtra("service_id", service.getId());
                context.startActivity(intent);
            }
        });
    }

    @Override
    public int getItemCount() {
        return serviceList.size();
    }

    public static class ViewHolder extends RecyclerView.ViewHolder {
        public ImageView serviceIcon;
        public TextView serviceName;
        public ViewHolder(View itemView) {
            super(itemView);
            this.serviceIcon = itemView.findViewById(R.id.service_icon);
            this.serviceName = itemView.findViewById(R.id.service_name);
        }
    }
}