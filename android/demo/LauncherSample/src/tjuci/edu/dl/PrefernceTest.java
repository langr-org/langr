package tjuci.edu.dl;

import android.os.Bundle;
import android.preference.PreferenceActivity;

public class PrefernceTest extends PreferenceActivity{
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		// TODO Auto-generated method stub
		super.onCreate(savedInstanceState);
		addPreferencesFromResource(R.xml.preferences);
	}
}
