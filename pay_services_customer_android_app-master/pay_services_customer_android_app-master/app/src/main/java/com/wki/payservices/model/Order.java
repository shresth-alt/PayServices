package com.wki.payservices.model;

public class Order {

    String orderNumber, serviceName, serviceDate, serviceStatus, paymentStatus;
    String address, city, landmark, postalCode;
    boolean isRated;


    public Order(String orderNumber, String serviceName, String serviceDate, String serviceStatus, String paymentStatus, String address, String city, String landmark, String postalCode) {
        this.orderNumber = orderNumber;
        this.serviceName = serviceName;
        this.serviceDate = serviceDate;
        this.serviceStatus = serviceStatus;
        this.paymentStatus = paymentStatus;
        this.address = address;
        this.city = city;
        this.landmark = landmark;
        this.postalCode = postalCode;
    }

    public String getOrderNumber() {
        return orderNumber;
    }

    public void setOrderNumber(String orderNumber) {
        this.orderNumber = orderNumber;
    }

    public String getServiceName() {
        return serviceName;
    }

    public void setServiceName(String serviceName) {
        this.serviceName = serviceName;
    }

    public String getServiceDate() {
        return serviceDate;
    }

    public void setServiceDate(String serviceDate) {
        this.serviceDate = serviceDate;
    }

    public String getServiceStatus() {
        return serviceStatus;
    }

    public void setServiceStatus(String serviceStatus) {
        this.serviceStatus = serviceStatus;
    }

    public String getPaymentStatus() {
        return paymentStatus;
    }

    public void setPaymentStatus(String paymentStatus) {
        this.paymentStatus = paymentStatus;
    }

    public String getAddress() {
        return address;
    }

    public void setAddress(String address) {
        this.address = address;
    }

    public String getCity() {
        return city;
    }

    public void setCity(String city) {
        this.city = city;
    }

    public String getLandmark() {
        return landmark;
    }

    public void setLandmark(String landmark) {
        this.landmark = landmark;
    }

    public String getPostalCode() {
        return postalCode;
    }

    public void setPostalCode(String postalCode) {
        this.postalCode = postalCode;
    }

    public boolean isRated() {
        return isRated;
    }

    public void setRated(boolean rated) {
        isRated = rated;
    }
}
