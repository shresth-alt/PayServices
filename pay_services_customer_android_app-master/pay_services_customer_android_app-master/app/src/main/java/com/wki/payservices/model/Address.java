package com.wki.payservices.model;

public class Address {

    String address, landmark, city, pincode, id;

    public Address(String address, String landmark, String city, String pincode, String id) {
        this.address = address;
        this.landmark = landmark;
        this.city = city;
        this.pincode = pincode;
        this.id = id;
    }

    public String getAddress() {
        return address;
    }

    public void setAddress(String address) {
        this.address = address;
    }

    public String getLandmark() {
        return landmark;
    }

    public void setLandmark(String landmark) {
        this.landmark = landmark;
    }

    public String getCity() {
        return city;
    }

    public void setCity(String city) {
        this.city = city;
    }

    public String getPincode() {
        return pincode;
    }

    public void setPincode(String pincode) {
        this.pincode = pincode;
    }

    public String getId() {
        return id;
    }
}
