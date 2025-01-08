package com.wki.payservicesvendor.model;

public class VendorPayment {
    String orderId, paymentType, transactionDate;
    double commissionAmount, orderAmount, serviceCharge, commission, commissionGST;

    public VendorPayment(String orderId, String paymentType, String transactionDate, double commissionAmount, double orderAmount,
                         double serviceCharge, double commission, double commissionGST) {
        this.orderId = orderId;
        this.paymentType = paymentType;
        this.transactionDate = transactionDate;
        this.commissionAmount = commissionAmount;
        this.orderAmount = orderAmount;
        this.serviceCharge = serviceCharge;
        this.commission = commission;
        this.commissionGST = commissionGST;
    }

    public String getOrderId() {
        return orderId;
    }

    public void setOrderId(String orderId) {
        this.orderId = orderId;
    }

    public String getPaymentType() {
        return paymentType;
    }

    public void setPaymentType(String paymentType) {
        this.paymentType = paymentType;
    }

    public String getTransactionDate() {
        return transactionDate;
    }

    public void setTransactionDate(String transactionDate) {
        this.transactionDate = transactionDate;
    }

    public double getCommissionAmount() {
        return commissionAmount;
    }

    public void setCommissionAmount(double commissionAmount) {
        this.commissionAmount = commissionAmount;
    }

    public double getOrderAmount() {
        return orderAmount;
    }

    public void setOrderAmount(double orderAmount) {
        this.orderAmount = orderAmount;
    }

    public double getServiceCharge() {
        return serviceCharge;
    }

    public double getCommission() {
        return commission;
    }

    public double getCommissionGST() {
        return commissionGST;
    }
}
