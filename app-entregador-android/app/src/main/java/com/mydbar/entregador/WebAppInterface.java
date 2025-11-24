package com.mydbar.entregador;

import android.content.Context;
import android.webkit.JavascriptInterface;
import android.widget.Toast;

public class WebAppInterface {
    Context mContext;

    WebAppInterface(Context c) {
        mContext = c;
    }

    @JavascriptInterface
    public void showToast(String toast) {
        Toast.makeText(mContext, toast, Toast.LENGTH_SHORT).show();
    }

    @JavascriptInterface
    public void vibrate(int duration) {
        android.os.Vibrator v = (android.os.Vibrator) 
            mContext.getSystemService(Context.VIBRATOR_SERVICE);
        if (v != null) {
            v.vibrate(duration);
        }
    }

    @JavascriptInterface
    public String getDeviceInfo() {
        return android.os.Build.MODEL + " - Android " + android.os.Build.VERSION.RELEASE;
    }
}
