//package org.test;

import java.util.*;
import java.io.*;
import org.json.*;

/**
 * FilesMap2Json 格式为3: map-array.
 * 1. array-array: [[time,n,s,h],[time,n,s,h],[time,n,s,h]]
 * 2. array-map: [{"time":time,"n":n,"s":s},{"time":time,"n":n,"s":s},{"time":time,"n":n,"s":s}]
 * 3. map-array: {"id1":[time,n,s],"id2":[time,n,s],"id3":[time,n,s]}
 * 4. map-map: {"id1":{"time":time,"n":n,"s":s},"id2":{"time":time,"n":n,"s":s},"id3":{"time":time,"n":n,"s":s}}
 *
 * map-array: {"filename":[90,1],"filename2":[90,3],"filename3":[90,1]}
 * map-map: {"filename":{"rotate":90,"count":1},"filename2":{"rotate":90,"count":3},"filename3":{"rotate":90,"count":1}}
 */
public class FilesMap2Json
{
	public static void main(String[] args)
	{
		FilesMap2Json t = new FilesMap2Json();
		System.out.println("FilesMap2Json");

		byte[] data = null;
		String json = null;
		String filename = "log.txt";
		String filename2 = "log2.txt";
		Map<String, JSONArray> map = null;
		int rotate = 0;

		data = t.readFile2(filename);
		json = (data == null) ? "" : new String(data);
		System.out.println("r:"+filename+" data:"+json);
		//json2map,json2list
		//JSONObject jmap = new JSONObject(json);
		/* 将json转换为map对象 */
		map = t.getMapForJson(json);

		rotate = t.file4Map(map, "DSC001.jpg");
		System.out.println("file:1:"+rotate);

		/* 添加或修改记录 */
		t.file2Map(map, "DSC001.jpg", rotate+90);
		t.file2Map(map, "DSC002.jpg", -90);
		t.file2Map(map, "DSC001.jpg", 10);
		//map2json,list2json
		try {
			data = new JSONObject(map).toString().getBytes();
		} catch (Exception e) {
			data = null;
			System.out.println(" data:"+map.size()+map.toString());
		}
		/* 覆盖到1，追加到2 */
		t.writeFile2(filename, data);
		t.writeFile2(filename2, data, true);
		/* 添加或修改记录 */
		/* 将指定的图片放入map表中，并保存到文件中 */
		t.file2Map(map, "DSC004.jpg", 180);
		try {
			data = new JSONObject(map).toString().getBytes();
		} catch (Exception e) {}
		t.writeFile2(filename, data);
		t.writeFile2(filename2, data, true);
	}

	/**
	 * 写文件，二进制适用
	 * @param append true 追加
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
	 * 读取文件内容，二进制适用
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
			/* 可读大小 */
			size = f.available();
			readBuf = new byte[size];
			while (size > 0 && r >= 0) {	/* -1 文件结束？ */
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
	 * 将相片旋转和播放记录放入Map, 最后转换为json:
	 * {"IMG001.jpg":[-90,3],"IMG002.jpg":[0,1]}
	 * Map<String filename, JSONArray jarr>
	 */
	public Map<String, JSONArray> file2Map(Map map, String filename, int rotate)
	{
		JSONArray jarr = null;
		try {
			jarr = (JSONArray) map.get(filename);
		} catch (Exception e) {}
		/* 不存在？ */
		try {
			if (jarr == null) {
				System.out.println("map null..."+map.size()+filename);
				/* 记录图片旋转，及播放次数值1 {"filename":[-90,1]} */
				jarr = new JSONArray().put(rotate).put(1);
				System.out.println("jarr ..."+jarr.toString());
			} else {
				System.out.println("jarr non-null..."+jarr.toString());
				/* 使用者自己在使用前加上旧的旋转值 */
				//jarr.put(0, jarr.getInt(0) + rotate);
				jarr.put(0, rotate);
				jarr.put(1, (int) jarr.get(1) + 1);
			}
			map.put(filename, jarr);
			System.out.println("map ..."+map.size());
		} catch (Exception e) {
			System.out.println("JSONException error...");
		}
		return map;
	}

	/**
	 * 在Map中找相片的旋转记录
	 */
	public int file4Map(Map map, String filename)
	{
		JSONArray jarr = null;
		try {
			jarr = (JSONArray) map.get(filename);
		} catch (Exception e) {}
		/* 不存在？ */
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

	/** 
	 * Json 转成 Map<> 
	 * @param jsonStr 
	 * @return 
	 */
	public static Map<String, JSONArray> getMapForJson(String jsonStr)
	{
		JSONObject jsonObject ;
		/* 当jsonStr为空或出错时，返回一个没有数据的Map对象，不是null */
		Map<String, JSONArray> valueMap = new HashMap<String, JSONArray>();

		try {
			jsonObject = new JSONObject(jsonStr);
			
			Iterator<String> keyIter= jsonObject.keys();
			String key;
			JSONArray value ;
			while (keyIter.hasNext()) {
				key = keyIter.next();
				value = (JSONArray) jsonObject.get(key);
				valueMap.put(key, value);
			}
		} catch (Exception e) {
			//e.printStackTrace();
		}
		return valueMap;
	}

	/** 
	 * Json 转成 List<Map<>> 2.
	 * 2. array-map: [{"time":time,"n":n,"s":s},{"time":time,"n":n,"s":s},{"time":time,"n":n,"s":s}]
	 * 3. map-array: {"id1":[time,n,s],"id2":[time,n,s],"id3":[time,n,s]}
	 * @param jsonStr 
	 * @return 
	 */
	public static List<Map<String, JSONArray>> getListForJson(String jsonStr)
	{
		List<Map<String, JSONArray>> list = new ArrayList<Map<String,JSONArray>>();
		//List<JSONArray> list = new ArrayList<JSONArray>();
		try {
			JSONArray jsonArray = new JSONArray(jsonStr);
			JSONObject jsonObj;
			for(int i = 0 ; i < jsonArray.length() ; i ++){
				jsonObj = (JSONObject)jsonArray.get(i);
				//list.add(jsonObj);
				list.add(getMapForJson(jsonObj.toString()));
			}
		} catch (Exception e) {
			// TODO: handle exception
			//e.printStackTrace();
		}
		return list;
	}
}
