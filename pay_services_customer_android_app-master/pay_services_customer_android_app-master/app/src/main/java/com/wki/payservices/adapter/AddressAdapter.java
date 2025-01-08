package com.wki.payservices.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AlertDialog;
import androidx.recyclerview.widget.RecyclerView;

import com.android.volley.AuthFailureError;
import com.android.volley.Request;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.StringRequest;
import com.wki.payservices.ApplicationController;
import com.wki.payservices.MyAddressesActivity;
import com.wki.payservices.MyBookingsActivity;
import com.wki.payservices.R;
import com.wki.payservices.SharedPreference;
import com.wki.payservices.Utils;
import com.wki.payservices.model.Address;
import com.wki.payservices.model.Order;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

public class AddressAdapter extends RecyclerView.Adapter<AddressAdapter.ViewHolder>{

    private List<Address> addressesList;
    Context context;
    SharedPreference sharedPreference = new SharedPreference();
    int deletedPosition = 0;

    public AddressAdapter(List<Address> addressesList, Context context) {
        this.addressesList = addressesList;
        this.context = context;
    }

    @Override
    public ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        LayoutInflater layoutInflater = LayoutInflater.from(parent.getContext());
        View listItem = layoutInflater.inflate(R.layout.address_item, parent, false);
        return new ViewHolder(listItem);
    }

    @Override
    public void onBindViewHolder(ViewHolder holder, int position) {
        final Address address = addressesList.get(position);
        holder.address.setText(address.getAddress());
        holder.landmark.setText(address.getLandmark());
        holder.city.setText(address.getCity());
        holder.pincode.setText(address.getPincode());
        holder.deleteAddress.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                deleteAddressAlert(address.getId());
                deletedPosition = position;
            }
        });
    }

    private void deleteAddressAlert(final String id) {
        AlertDialog.Builder mBuilder = new AlertDialog.Builder(context);
        mBuilder.setTitle("Do you really want to delete the address?");
        mBuilder.setPositiveButton("yes", (arg0, arg1)-> {
            deleteAddress(id);
        });
        mBuilder.setNegativeButton("no", null);
        AlertDialog alertDialog = mBuilder.create();
        alertDialog.show();
    }

    private void deleteAddress(String addressId) {
        StringRequest strReq = new StringRequest(Request.Method.DELETE, Utils.DOMAIN + "/api/v1/user-addresses/" + addressId ,
                new Response.Listener<String>() {
                    @Override
                    public void onResponse(String response) {
                        Toast.makeText(context, "Address has been deleted", Toast.LENGTH_SHORT).show();
                        addressesList.remove(deletedPosition);
                        notifyDataSetChanged();
                    }
                }, new Response.ErrorListener() {
                    @Override
                    public void onErrorResponse(VolleyError error) {
                        Toast.makeText(context, "Something went wrong! Please try again", Toast.LENGTH_SHORT).show();
                    }
                }
        ){
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                HashMap<String, String> headers = new HashMap<>();
                headers.put("Authorization", "Bearer " + sharedPreference.getRememberToken(context));
                return headers;
            }
        };
        strReq.setShouldCache(false);
        ApplicationController.getInstance().addToRequestQueue(strReq, Utils.VOLLEY_REQUESTS);;
    }

    @Override
    public int getItemCount() {
        return addressesList.size();
    }

    public static class ViewHolder extends RecyclerView.ViewHolder {
        public TextView address, landmark, city, pincode, deleteAddress;
        public ViewHolder(View itemView) {
            super(itemView);
            this.address = itemView.findViewById(R.id.address);
            this.landmark = itemView.findViewById(R.id.landmark);
            this.city = itemView.findViewById(R.id.city);
            this.pincode = itemView.findViewById(R.id.pincode);
            this.deleteAddress = itemView.findViewById(R.id.delete_address);
        }
    }
}