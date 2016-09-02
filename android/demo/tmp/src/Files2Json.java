//package org.test;

import java.util.*;
import java.io.*;
import org.json.*;

/**
 * ֱ���� JSONObject �� Map(HashMap) �� 
 * �� JSONArray �� ArrayList �� 
 *
 * Files2Json ��ʽΪ3: map-array.
 * 1. array-array: [[time,n,s,h],[time,n,s,h],[time,n,s,h]]
 * 2. array-map: [{"time":time,"n":n,"s":s},{"time":time,"n":n,"s":s},{"time":time,"n":n,"s":s}]
 * 3. map-array: {"id1":[time,n,s],"id2":[time,n,s],"id3":[time,n,s]}
 * 4. map-map: {"id1":{"time":time,"n":n,"s":s},"id2":{"time":time,"n":n,"s":s},"id3":{"time":time,"n":n,"s":s}}
 *
 * map-array: {"filename":[90,1],"filename2":[90,3],"filename3":[90,1]}
 * map-map: {"filename":{"rotate":90,"count":1},"filename2":{"rotate":90,"count":3},"filename3":{"rotate":90,"count":1}}
 *
 * $Id$
 */
public class Files2Json
{
	public static void main(String[] args)
	{
		Files2Json rotateRec = new Files2Json("org.langr.test");
		String key = "DSC001.jpg";
		int oldRotate = rotateRec.getRotate(key);
		System.out.println("key:"+key+" old:"+oldRotate);
		rotateRec.setRotate(key, oldRotate + 90);
		oldRotate = rotateRec.getRotate(key);
		System.out.println("key:"+key+" new:"+oldRotate);
	}

	public static void main2(String[] args)
	{
		Files2Json t = new Files2Json();
		System.out.println("Files2Json");

		byte[] data = null;
		String json = null;
		String filename = "log-json.txt";
		String filename2 = "log-json2.txt";
		JSONObject jmap = null;
		int rotate = 0;

		data = t.readFile2(filename);
		json = (data == null) ? "" : new String(data);
		System.out.println("r:"+filename+" data:"+json);
		//json2map,json2list
		try {
			jmap = new JSONObject(json);
		} catch (Exception e) {
			jmap = new JSONObject();
		}

		rotate = t.file4Json(jmap, "DSC001.jpg");
		System.out.println("file:1:"+rotate);

		/* ��ӻ��޸ļ�¼ */
		t.file2Json(jmap, "DSC001.jpg", rotate+90);
		t.file2Json(jmap, "DSC002.jpg", -90);
		//t.file2Json(jmap, "DSC001.jpg", 10);
		//map2json,list2json
		try {
			data = jmap.toString().getBytes();
		} catch (Exception e) {
			data = null;
			System.out.println(" data:"+jmap.length()+jmap.toString());
		}
		/* ���ǵ�1��׷�ӵ�2 */
		t.writeFile2(filename, data);
		t.writeFile2(filename2, data, true);
		/* ��ӻ��޸ļ�¼ */
		/* ��ָ����ͼƬ����map���У������浽�ļ��� */
		t.file2Json(jmap, "DSC004.jpg", 180);
		try {
			data = jmap.toString().getBytes();
		} catch (Exception e) {}
		t.writeFile2(filename, data);
		t.writeFile2(filename2, data, true);
	}

	/* Ĭ�ϱ����ļ��� */
	private String mFilename = null;
	private JSONObject jmap = null;

	public Files2Json() {
		this("/sdcard/org.langr.idx.txt");
	}

	public Files2Json(String filename) {
		mFilename = filename;

		byte[] data = null;
		int rotate = 0;

		data = readFile2(mFilename);
		try {
			jmap = new JSONObject(new String(data));
		} catch (Exception e) {
			jmap = new JSONObject();
		}
	}

	/**
	 * ��ȡ��תֵ
	 */
	public int getRotate(String key)
	{
		return file4Json(jmap, key);
	}

	/**
	 * ������תֵ
	 */
	public int setRotate(String key, int rotate)
	{
		byte[] data = null;
		file2Json(jmap, key, rotate);
		try {
			data = jmap.toString().getBytes();
		} catch (Exception e) {
			data = null;
		}
		/* ����д���ļ� */
		writeFile2(mFilename, data);
		return 0;
	}

	/**
	 * д�ļ�������������
	 * @param append true ׷��
	 */
	public boolean writeFile2(String filename, byte[] data)
	{
		return writeFile2(filename, data, false);
	}

	public boolean writeFile2(String filename, byte[] data, boolean append)
	{
		//byte[] data = String data.getBytes();
		OutputStream f = null;
		try {
			f = new FileOutputStream(filename, append);
			//f.write(data, 0, data.length);
			f.write(data);
			f.close();
		} catch (Exception e) {
			System.out.println("open write error:"+filename);
			if (f != null) {
				System.out.println("write error:"+filename);
				try {
					f.close();
				} catch (Exception e2) {
				}
			}
					e.printStackTrace();
			return false;
		}
		return true;
	}

	/**
	 * ��ȡ�ļ����ݣ�����������
	 */
	public byte[] readFile2(String filename)
	{
		byte[] readBuf = null;
		int size = 0;
		int offset = 0;
		int r = 0;	/* */
		InputStream f = null;
		try {
			f = new FileInputStream(filename);
			/* �ɶ���С */
			size = f.available();
			readBuf = new byte[size];
			while (size > 0 && r >= 0) {	/* -1 �ļ������� */
				//(offset<bytes.length&&(numRead=is.read(bytes,offset,bytes.length-offset))>=0 );
				r = f.read(readBuf, offset, size);
				System.out.println("read:"+filename+" size:"+size+" offset:"+offset+" read:"+r);
				offset += r;
				size -= r;
			}
			f.close();
		} catch (Exception e) {
			/* error */
			System.out.println("open read error:"+filename);
			if (f != null) {
				System.out.println("read error:"+filename);
			}
			//e.printStackTrace();
		}
		//return String ret = EncodingUtils.getString(readBuf, "UTF-8");
		return readBuf;
	}

	/**
	 * Map => JSONObject
	 * List => JSONArray
	 * ����Ƭ��ת�Ͳ��ż�¼����JSONObject:
	 * {"IMG001.jpg":[-90,3],"IMG002.jpg":[0,1]}
	 * JSONObject: <String filename, JSONArray jarr>
	 */
	public JSONObject file2Json(JSONObject jmap, String filename, int rotate)
	{
		JSONArray jarr = null;
		try {
			jarr = (JSONArray) jmap.get(filename);
		} catch (Exception e) {}

		if (rotate >= 360) {
			rotate -= 360;
		} else if (rotate <= -360) {
			rotate += 360;
		}
		/* �����ڣ� */
		try {
			if (jarr == null) {
				System.out.println("map null..."+jmap.length()+filename);
				/* ��¼ͼƬ��ת�������Ŵ���ֵ1 {"filename":[-90,1]} */
				jarr = new JSONArray().put(rotate).put(1);
				System.out.println("jarr ..."+jarr.toString());
			} else {
				System.out.println("jarr non-null..."+jarr.toString());
				/* ʹ�����Լ���ʹ��ǰ���Ͼɵ���תֵ */
				//jarr.put(0, jarr.getInt(0) + rotate);
				jarr.put(0, rotate);
				jarr.put(1, jarr.getInt(1) + 1);
			}
			jmap.put(filename, jarr);
			System.out.println("jmap ..."+jmap.length());
		} catch (Exception e) {
			System.out.println("JSONException error...");
		}
		return jmap;
	}

	/**
	 * ��JSONObject������Ƭ����ת��¼
	 */
	public int file4Json(JSONObject jmap, String filename)
	{
		JSONArray jarr = null;
		try {
			jarr = (JSONArray) jmap.get(filename);
		} catch (Exception e) {}
		/* �����ڣ� */
		if (jarr == null) {
			return 0;
		}
		int rotate = 0;
		try {
			rotate = jarr.getInt(0);
		} catch (Exception e) {
		}
		return rotate;
	}
}
