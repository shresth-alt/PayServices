package com.wki.payservicesvendor;

import androidx.annotation.NonNull;
import androidx.annotation.RequiresApi;
import androidx.appcompat.app.AlertDialog;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;
import androidx.core.app.ActivityCompat;
import androidx.core.content.ContextCompat;
import android.Manifest;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.graphics.Bitmap;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.provider.MediaStore;
import android.util.Base64;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.MenuItem;
import android.view.View;
import android.view.animation.AnimationUtils;
import android.widget.Button;
import android.widget.CompoundButton;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RadioGroup;
import android.widget.TextView;
import android.widget.Toast;
import com.android.volley.NetworkResponse;
import com.android.volley.Request;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.StringRequest;
import com.google.gson.Gson;
import com.wki.payservicesvendor.model.AdditionalProduct;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.nio.charset.StandardCharsets;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import io.github.inflationx.viewpump.ViewPumpContextWrapper;

public class CompleteOrderActivity extends AppCompatActivity implements View.OnClickListener {

    LinearLayout layoutAddProduct, layoutPersonalDetail, layoutProductDetail;
    EditText etName, etEmail, etMobile, etAltMobile, etVillage, etLandmark, etCity, etState, etPinCode;
    String thisOrderNumber;
    int productCount = 0;
    int idProductName = 1001, idProductCost = 2002, idProductImage = 3003;
    Map<Integer, Uri> uriMap = new HashMap<>();
    static final int REQUEST_MAIN_IMAGE = 1122;
    static final int REQUEST_SERIAL_MODEL_IMAGE = 1133;
    static final int STORAGE_PERMISSION_REQUEST = 9;
    ImageView productMainImage, serialModelImage;
    Uri mainImageUri, serialModelUri;
    String mainImageBase64, serialModelImageBase64;
    SharedPreference sharedPreference = new SharedPreference();
    Gson gson = new Gson();
    Toolbar toolbar;
    com.google.android.material.checkbox.MaterialCheckBox checkOrderHasItems;
    RadioGroup groupChargeType;
    TextView calculateEstimate;
    String thisServiceName;
    int thisServiceCharge;

    @Override
    protected void attachBaseContext(Context newBase) {
        super.attachBaseContext(ViewPumpContextWrapper.wrap(newBase));
    }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_complete_order);
        toolbar = findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);
        etName = findViewById(R.id.ed_name);
        etEmail = findViewById(R.id.ed_email);
        etMobile = findViewById(R.id.ed_mobile);
        etAltMobile = findViewById(R.id.ed_alternate_mobile);
        etVillage = findViewById(R.id.ed_village);
        etLandmark = findViewById(R.id.ed_landmark);
        etCity = findViewById(R.id.ed_city);
        etState = findViewById(R.id.ed_state);
        etPinCode = findViewById(R.id.ed_pin_code);

        layoutAddProduct = findViewById(R.id.layout_add_product);
        layoutPersonalDetail = findViewById(R.id.layout_personal_detail);
        layoutProductDetail = findViewById(R.id.layout_product_detail);
        productMainImage = findViewById(R.id.product_main_image);
        serialModelImage = findViewById(R.id.serial_model_image);
        productMainImage.setOnClickListener(this);
        serialModelImage.setOnClickListener(this);

        calculateEstimate = findViewById(R.id.calculate_estimate);
        calculateEstimate.setOnClickListener(this);

        findViewById(R.id.btn_add_more).setOnClickListener(this);
        ((Button)findViewById(R.id.btn_add_more)).setTransformationMethod(null);
        findViewById(R.id.btn_submit).setOnClickListener(this);
        findViewById(R.id.btn_next).setOnClickListener(this);
        layoutPersonalDetail.setVisibility(View.VISIBLE);
        layoutProductDetail.setVisibility(View.GONE);
        thisOrderNumber = getIntent().getStringExtra("order_id");
        toolbar = findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);
        if (getSupportActionBar() != null) {
            getSupportActionBar().setDisplayHomeAsUpEnabled(true);
            getSupportActionBar().setTitle("Order #"+thisOrderNumber);
        }
        groupChargeType = findViewById(R.id.service_charge_types);
        checkOrderHasItems = findViewById(R.id.order_has_items);
        checkOrderHasItems.setOnCheckedChangeListener(new CompoundButton.OnCheckedChangeListener() {
            @Override
            public void onCheckedChanged(CompoundButton buttonView, boolean isChecked) {
                if (isChecked) {
                    findViewById(R.id.items_wrapper).setVisibility(View.VISIBLE);
                    calculateEstimate.setVisibility(View.VISIBLE);
                } else {
                    findViewById(R.id.items_wrapper).setVisibility(View.GONE);
                    calculateEstimate.setVisibility(View.GONE);
                }
            }
        });
        getOrderDetails();
    }

    private void getOrderDetails() {
        if (Utils.isConnected(this)) {
            StringRequest strReq = new StringRequest(Request.Method.GET, Utils.DOMAIN + "/api/v1/orders/" + thisOrderNumber, new Response.Listener<String>() {
                @Override
                public void onResponse(String response) {
                    try {
                        JSONObject object = new JSONObject(response);
                        etName.setText(object.getString("name"));
                        etName.setEnabled(false);
                        if (!object.isNull("email")) etEmail.setText(object.getString("email"));
                        etMobile.setText(object.getString("mobile"));
                        etMobile.setEnabled(false);
                        if (!object.isNull("alt_mobile")) etAltMobile.setText(object.getString("alt_mobile"));
                        if (!object.isNull("customer_village")) etVillage.setText(object.getString("customer_village"));
                        if (!object.isNull("customer_landmark")) etLandmark.setText(object.getString("customer_landmark"));
                        if (!object.isNull("customer_city")) etCity.setText(object.getString("customer_city"));
                        if (!object.isNull("customer_state")) etState.setText(object.getString("customer_state"));
                        if (!object.isNull("customer_pincode")) etPinCode.setText(object.getString("customer_pincode"));
                        if (!object.isNull("customer_state")) etState.setText(object.getString("customer_state"));

                        thisServiceName = object.getString("service_name");
                        thisServiceCharge = object.getInt("charges");

                    } catch (JSONException e) {
                        e.printStackTrace();
                        Toast.makeText(CompleteOrderActivity.this, R.string.something_went_wrong, Toast.LENGTH_SHORT).show();
                    }
                }
            }, new Response.ErrorListener() {
                @RequiresApi(api = Build.VERSION_CODES.KITKAT)
                @Override
                public void onErrorResponse(VolleyError error) {
                    NetworkResponse networkResponse = error.networkResponse;
                    if (networkResponse != null) {
                        int errorCode = networkResponse.statusCode;
                        String responseBody = new String(error.networkResponse.data, StandardCharsets.UTF_8);
                    }
                }
            }) {
                @Override
                public Map<String, String> getHeaders() {
                    HashMap<String, String> headers = new HashMap<>();
                    headers.put("Authorization", "Bearer " + sharedPreference.getRememberToken(CompleteOrderActivity.this));
                    return headers;
                }
            };
            ApplicationController.getInstance().addToRequestQueue(strReq, Utils.VOLLEY_REQUESTS);

        } else {
            Toast.makeText(this, R.string.connection_error, Toast.LENGTH_SHORT).show();
        }
    }

    private void addNewProduct() {
        productCount++;
        final View productView = LayoutInflater.from(CompleteOrderActivity.this).inflate(R.layout.layout_product_detail, null);
        layoutAddProduct.addView(productView);
        String title = "Enter item details";
        ((TextView) productView.findViewById(R.id.title)).setText(title);
        productView.findViewById(R.id.ed_product_name).setId(++idProductName);
        productView.findViewById(R.id.ed_product_cost).setId(++idProductCost);
        final ImageView ivRemove = productView.findViewById(R.id.delete);
        ivRemove.setId(productCount);
        ivRemove.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                layoutAddProduct.removeView(productView);
                /*
                if (layoutAddProduct.getChildCount() == 1) {
                    Toast.makeText(CompleteOrderActivity.this, "Cannot delete this item", Toast.LENGTH_SHORT).show();
                } else {
                    layoutAddProduct.removeView(productView);
                }
                */
            }
        });
        final ImageView productPhoto = productView.findViewById(R.id.product_photo);
        productPhoto.setId(++idProductImage);
        productPhoto.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                chooseImage(productPhoto.getId());
            }
        });
    }

    @Override
    public void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (resultCode == RESULT_OK && data != null) {
            Uri fileUri;
            if (data.getData() != null) {
                fileUri = data.getData();
            } else {
                Bitmap bitmap = (Bitmap) data.getExtras().get("data");
                String path = MediaStore.Images.Media.insertImage(getApplicationContext().getContentResolver(), bitmap, "Title", null);
                fileUri = Uri.parse(path);
            }
            if (requestCode == REQUEST_MAIN_IMAGE) {
                mainImageUri = fileUri;
                productMainImage.setImageURI(mainImageUri);
            } else if (requestCode == REQUEST_SERIAL_MODEL_IMAGE) {
                serialModelUri = fileUri;
                serialModelImage.setImageURI(serialModelUri);
            } else {
                ImageView ivImageView = ((ImageView) findViewById(idProductImage));
                if (ivImageView != null) {
                    ivImageView.setImageURI(fileUri);
                    uriMap.put(idProductImage, fileUri);
                }
            }
        }
    }

    String personalJsonString;

    private void submitData() {
        Utils.showLoader(this, null);
        int nameID = 1001, costID = 2002, imageID = 3003;
        final List<AdditionalProduct> productList = new ArrayList<>();
        int viewCount = layoutAddProduct.getChildCount();
        for (int i = 1; i <= viewCount; i++) {
            EditText etViewName = findViewById(++nameID);
            EditText etViewCost = findViewById(++costID);
            if (etViewName != null && etViewCost != null) {
                String name = etViewName.getText().toString();
                String costStr = etViewCost.getText().toString();
                Uri uri = uriMap.get(++imageID);
                String base64Image = getString(R.string.empty);
                try {
                    if (uri != null && uri.getPath() != null && !uri.getPath().isEmpty()) {
                        base64Image = getBase64Image(uri);
                    }
                } catch (IOException e) {
                    e.printStackTrace();
                    Toast.makeText(this, R.string.something_went_wrong, Toast.LENGTH_SHORT).show();
                }
                int cost = 0;
                if (!costStr.isEmpty()) cost = Integer.parseInt(costStr);
                productList.add(new AdditionalProduct(name, cost, uri, base64Image));

            } else {
                Toast.makeText(this, R.string.something_went_wrong, Toast.LENGTH_SHORT).show();
            }
        }

        for (AdditionalProduct additionalProduct : productList) {
            Log.d("_PARAMAS_LOOPER_" + additionalProduct.getCost(), "N: " + additionalProduct.getName());
        }

        mainImageBase64 = getString(R.string.empty);
        serialModelImageBase64 = getString(R.string.empty);
        try {
            if (mainImageUri != null && mainImageUri.getPath() != null && !mainImageUri.getPath().isEmpty()) {
                mainImageBase64 = getBase64Image(mainImageUri);
            }
            if (serialModelUri != null && serialModelUri.getPath() != null && !serialModelUri.getPath().isEmpty()) {
                serialModelImageBase64 = getBase64Image(serialModelUri);
            }
        } catch (IOException e) {
            e.printStackTrace();
            Toast.makeText(this, R.string.something_went_wrong, Toast.LENGTH_SHORT).show();
        }
        StringRequest strReq = new StringRequest(Request.Method.PUT, Utils.DOMAIN + "/api/v1/complete-order/"+thisOrderNumber, new Response.Listener<String>() {
            @Override
            public void onResponse(String response) {
                try {
                    JSONObject object = new JSONObject(response);
                    Toast.makeText(CompleteOrderActivity.this, object.getString("message"), Toast.LENGTH_SHORT).show();
                    finish();
                } catch (JSONException e) {
                    e.printStackTrace();
                    Toast.makeText(CompleteOrderActivity.this, e.toString(), Toast.LENGTH_SHORT).show();
                }
                Utils.hideLoader(CompleteOrderActivity.this);
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError error) {
                String json;
                NetworkResponse response = error.networkResponse;
                if (response != null && response.data != null) {
                    json = new String(response.data);
                    try {
                        JSONObject errorObject = new JSONObject(json);
                        JSONArray error_ = errorObject.getJSONArray("errors");
                        Toast.makeText(CompleteOrderActivity.this, error_.get(0).toString(), Toast.LENGTH_LONG).show();
                    } catch (JSONException e) {
                        Toast.makeText(CompleteOrderActivity.this, "Something went wrong on server", Toast.LENGTH_SHORT).show();
                        e.printStackTrace();
                    }
                }

                Utils.hideLoader(CompleteOrderActivity.this);
            }
        }) {
            @Override
            public Map<String, String> getHeaders() {
                HashMap<String, String> headers = new HashMap<>();
                headers.put("Authorization", "Bearer " + sharedPreference.getRememberToken(CompleteOrderActivity.this));
                return headers;
            }
            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<>();
                params.put("items", gson.toJson(productList));
                params.put("charge_type", chargeType);
                params.put("main_image", mainImageBase64);
                params.put("serial_model_image", serialModelImageBase64);
                params.put("email", etEmail.getText().toString().trim());
                params.put("alt_mobile", etAltMobile.getText().toString().trim());
                params.put("village", etVillage.getText().toString().trim());
                params.put("landmark", etLandmark.getText().toString().trim());
                params.put("city", etCity.getText().toString().trim());
                params.put("state", etState.getText().toString().trim());
                params.put("pin_code", etPinCode.getText().toString().trim());
                return params;
            }
        };
        ApplicationController.getInstance().addToRequestQueue(strReq, Utils.VOLLEY_REQUESTS);
    }

    private Bitmap getScaledImage(Bitmap bitmap) {
        int width = bitmap.getWidth();
        int height = bitmap.getHeight();
        int maxWidth = 800, maxHeight = 800;
        float ratioBitmap = (float) width / (float) height;
        float ratioMax = (float) maxWidth / (float) maxHeight;
        int finalWidth = maxWidth;
        int finalHeight = maxHeight;
        if (ratioMax > ratioBitmap) {
            finalWidth = (int) ((float)maxHeight * ratioBitmap);
        } else {
            finalHeight = (int) ((float)maxWidth / ratioBitmap);
        }
        bitmap = Bitmap.createScaledBitmap(bitmap, finalWidth, finalHeight, true);
        return bitmap;
    }

    private String getBase64Image(Uri uri) throws IOException {
        ByteArrayOutputStream outputStream = new ByteArrayOutputStream();
        Bitmap bitmap = MediaStore.Images.Media.getBitmap(getContentResolver(), uri);
        bitmap = getScaledImage(bitmap);
        bitmap.compress(Bitmap.CompressFormat.PNG, 100, outputStream);
        return Base64.encodeToString(outputStream.toByteArray(), Base64.DEFAULT);
    }

    private void setLayoutVisibility(boolean backPressed) {
        if (backPressed) {
            productCount = 0;
            layoutAddProduct.removeAllViews();
            if (layoutPersonalDetail.getVisibility() == View.VISIBLE) {
                finish();
            } else {
                layoutProductDetail.setVisibility(View.GONE);
                layoutProductDetail.setAnimation(AnimationUtils.loadAnimation(this, R.anim.exit_to_left));
                layoutPersonalDetail.setVisibility(View.VISIBLE);
                layoutPersonalDetail.setAnimation(AnimationUtils.loadAnimation(this, R.anim.enter_from_right));
            }
        } else {
            if (layoutPersonalDetail.getVisibility() == View.VISIBLE) {
                layoutPersonalDetail.setVisibility(View.GONE);
                layoutPersonalDetail.setAnimation(AnimationUtils.loadAnimation(this, R.anim.exit_to_left));
                layoutProductDetail.setVisibility(View.VISIBLE);
                layoutProductDetail.setAnimation(AnimationUtils.loadAnimation(this, R.anim.enter_from_right));
            } else {
                layoutProductDetail.setVisibility(View.GONE);
                layoutProductDetail.setAnimation(AnimationUtils.loadAnimation(this, R.anim.exit_to_left));
                layoutPersonalDetail.setVisibility(View.VISIBLE);
                layoutPersonalDetail.setAnimation(AnimationUtils.loadAnimation(this, R.anim.enter_from_right));
            }
        }
    }

    @Override
    public void onClick(View v) {

        switch (v.getId()) {
            case R.id.btn_add_more:
                addNewProduct();
                break;
            case R.id.calculate_estimate:
                calculateEstimate();
                break;
            case R.id.product_main_image:
                chooseImage(REQUEST_MAIN_IMAGE);
                break;
            case R.id.serial_model_image:
                chooseImage(REQUEST_SERIAL_MODEL_IMAGE);
                break;
            case R.id.btn_submit:
                promptUser();
                break;
            case R.id.btn_next:
                getProfileDetail();
                break;
        }
    }

    private void calculateEstimate() {
        int costID = 2002;
        List<Integer> costList = new ArrayList<>();
        int viewCount = layoutAddProduct.getChildCount();
        if (viewCount > 0) {
            for (int i = 1; i <= viewCount; i++) {
                EditText etViewCost = findViewById(++costID);
                if (etViewCost != null) {
                    String costStr = etViewCost.getText().toString();
                    int cost = 0;
                    if (!costStr.isEmpty()) cost = Integer.parseInt(costStr);
                    costList.add(cost);
                } else {
                    Toast.makeText(this, R.string.something_went_wrong, Toast.LENGTH_SHORT).show();
                }
            }

            int partsCost = 0;
            for (Integer eachCost : costList) {
                partsCost += eachCost;
            }

            int totalCost = partsCost+thisServiceCharge;

            if (partsCost != 0) {

                AlertDialog.Builder aDialog = new AlertDialog.Builder(this);
                aDialog.setTitle("Total cost: "+totalCost);
                String costDetail = "Service: " + thisServiceName + "\n"
                        + "Service Charge: " + thisServiceCharge + "\n"
                        + "Parts cost: " + partsCost;
                aDialog.setMessage(costDetail);
                aDialog.setCancelable(false);
                aDialog.setNegativeButton("OK", null);
                aDialog.show();
            }

        } else {
            Toast.makeText(this, "No items added", Toast.LENGTH_SHORT).show();
        }
    }

    private void chooseImage(final int requestCode) {

        if (hasPermission()) {

            final String[] options = getResources().getStringArray(R.array.image_chooser);
            AlertDialog.Builder alertDialog = new AlertDialog.Builder(CompleteOrderActivity.this);
            alertDialog.setItems(options, new DialogInterface.OnClickListener() {
                @Override
                public void onClick(DialogInterface dialog, int checkedItem) {

                    if (checkedItem == 0) {
                        Intent takePictureIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
                        startActivityForResult(takePictureIntent, requestCode);
                    } else {
                        Intent intent = new Intent();
                        intent.setType("image/*");
                        intent.setAction(Intent.ACTION_GET_CONTENT);
                        startActivityForResult(Intent.createChooser(intent, "Select Picture"), requestCode);
                    }
                }
            });
            alertDialog.show();

        } else askPermission();
    }

    private void getProfileDetail() {

        boolean invalidData = /*etName.getText().toString().trim().isEmpty() ||
                etEmail.getText().toString().trim().isEmpty() ||
                etMobile.getText().toString().trim().isEmpty() ||
                etAltMobile.getText().toString().trim().isEmpty() ||
                etVillage.getText().toString().trim().isEmpty() ||
                etLandmark.getText().toString().trim().isEmpty() ||
                etCity.getText().toString().trim().isEmpty() ||
                etState.getText().toString().trim().isEmpty() ||
                etPinCode.getText().toString().trim().isEmpty()*/ false;

        if (invalidData) {
            Toast.makeText(this, "Please fill all the details", Toast.LENGTH_SHORT).show();
        } else {
            setLayoutVisibility(false);
        }
    }

    String chargeType;
    private void promptUser() {

        if (!Utils.isConnected(this)) {
            Toast.makeText(this, R.string.connection_error, Toast.LENGTH_SHORT).show();
            return;
        }

        if (groupChargeType.getCheckedRadioButtonId() == R.id.visiting_charge) chargeType = "visiting";
        else if (groupChargeType.getCheckedRadioButtonId() == R.id.service_charge) chargeType = "service";

        if (chargeType == null || chargeType.isEmpty()) {
            Toast.makeText(this, R.string.charge_type_error, Toast.LENGTH_SHORT).show();
            return;
        }

        AlertDialog.Builder alertDialog = new AlertDialog.Builder(this);
        alertDialog.setMessage("Are you sure to complete this order?");
        alertDialog.setCancelable(false);
        alertDialog.setNegativeButton("No", null);
        alertDialog.setPositiveButton("Yes", new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int which) {
                submitData();
            }
        });
        alertDialog.show();
    }

    private boolean hasPermission() {
        if (Build.VERSION.SDK_INT >= android.os.Build.VERSION_CODES.M) {
            return (ContextCompat.checkSelfPermission(this, Manifest.permission.READ_EXTERNAL_STORAGE) == PackageManager.PERMISSION_GRANTED)
                    && (ContextCompat.checkSelfPermission(this, Manifest.permission.WRITE_EXTERNAL_STORAGE) == PackageManager.PERMISSION_GRANTED)
                    && (ContextCompat.checkSelfPermission(this, Manifest.permission.CAMERA) == PackageManager.PERMISSION_GRANTED);
        } else {
            return true;
        }
    }

    private void askPermission() {
        if (Build.VERSION.SDK_INT >= android.os.Build.VERSION_CODES.M) {
            ActivityCompat.requestPermissions(this, new String[] {
                    Manifest.permission.READ_EXTERNAL_STORAGE,
                    Manifest.permission.WRITE_EXTERNAL_STORAGE,
                    Manifest.permission.CAMERA
            }, STORAGE_PERMISSION_REQUEST);
        }
    }

    @Override
    public boolean onOptionsItemSelected(@NonNull MenuItem item) {
        int id = item.getItemId();
        if (id == android.R.id.home) {
            setLayoutVisibility(true);
        }
        return super.onOptionsItemSelected(item);
    }
}