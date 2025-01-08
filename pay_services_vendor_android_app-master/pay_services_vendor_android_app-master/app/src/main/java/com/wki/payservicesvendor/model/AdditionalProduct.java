package com.wki.payservicesvendor.model;

import android.net.Uri;
import com.google.gson.annotations.SerializedName;

public class AdditionalProduct {

    @SerializedName("item_name")
    String name;

    @SerializedName("price")
    int cost;

    @SerializedName("image")
    String base64Image;

    Uri uri;

    public AdditionalProduct(String name, int cost, Uri uri, String base64Image) {
        this.name = name;
        this.cost = cost;
        this.uri = uri;
        this.base64Image = base64Image;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public int getCost() {
        return cost;
    }

    public void setCost(int cost) {
        this.cost = cost;
    }

    public Uri getUri() {
        return uri;
    }

    public void setUri(Uri uri) {
        this.uri = uri;
    }

    public String getBase64Image() {
        return base64Image;
    }

    public void setBase64Image(String base64Image) {
        this.base64Image = base64Image;
    }
}
