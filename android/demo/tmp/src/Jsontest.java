//package org.test;

import java.util.*;
import java.io.*;
import org.json.*;

/**
 * array-array: [[time,n,s],[time,n,s],[time,n,s]]
 * array-map: [{"time":time,"n":n,"s":s},{"time":time,"n":n,"s":s},{"time":time,"n":n,"s":s}]
 * map-array: {"id1":[time,n,s],"id2":[time,n,s],"id3":[time,n,s]}
 * map-map: {"id1":{"time":time,"n":n,"s":s},"id2":{"time":time,"n":n,"s":s},"id3":{"time":time,"n":n,"s":s}}
 *
 * map-array: {"filename":[90,1],"filename2":[90,3],"filename3":[90,1]}
 * map-map: {"filename":{"rotate":90,"count":1},"filename2":{"rotate":90,"count":3},"filename3":{"rotate":90,"count":1}}
 */
public class Jsontest
{
	public static void main(String []args) 
	{
		Jsontest t = new Jsontest();
		//t.map2Json();
		System.out.println("Hello");
		//t.json2o();
		//t.listmap();
		byte[] data = null;
		String json = null;
		String filename = "log.txt";
		String filename2 = "log2.txt";
		Map<String, JSONArray> map = null;

		data = t.readFile2(filename);
		json = new String(data);
		System.out.println("r:"+filename+" data:"+json);
		//json2map,json2list
		//JSONObject jmap = new JSONObject(json);
		map = t.getMapForJson(json);

		t.file2Map(map, "DSC001.jpg", 90);
		t.file2Map(map, "DSC002.jpg", -90);
		t.file2Map(map, "DSC001.jpg", 10);
		//map2json,list2json
		data = new JSONObject(map).toString().getBytes();
		t.writeFile2(filename, data);
		t.writeFile2(filename, data, true);
		t.writeFile2(filename2, data);
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
			e.printStackTrace();
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
				System.out.println("map null...");
				/* 记录图片旋转，及播放次数值1 {"filename":[-90,1]} */
				jarr = new JSONArray().put(rotate).put(1);
				System.out.println("jarr ..."+jarr.toString());
			} else {
				System.out.println("jarr non-null..."+jarr.toString());
				jarr.put(0, jarr.getInt(0) + rotate);
				jarr.put(1, (int) jarr.get(1) + 1);
			}
			map.put(filename, jarr);
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
	public static Map<String, Object> getMapForJson(String jsonStr)
	{
		JSONObject jsonObject ;
		try {
			jsonObject = new JSONObject(jsonStr);
			
			Iterator<String> keyIter= jsonObject.keys();
			String key;
			Object value ;
			Map<String, Object> valueMap = new HashMap<String, Object>();
			while (keyIter.hasNext()) {
				key = keyIter.next();
				value = jsonObject.get(key);
				valueMap.put(key, value);
			}
			return valueMap;
		} catch (Exception e) {
			// TODO: handle exception
			e.printStackTrace();
		}
		//return null;
		return  new HashMap<String, Object>();
	}

	/** 
	 * Json 转成 List<Map<>> 
	 * @param jsonStr 
	 * @return 
	 */
	public static List<Map<String, Object>> getListForJson(String jsonStr)
	{
		List<Map<String, Object>> list = null;
		//List<Object> list = null;
		try {
			JSONArray jsonArray = new JSONArray(jsonStr);
			JSONObject jsonObj;
			list = new ArrayList<Map<String,Object>>();
			for(int i = 0 ; i < jsonArray.length() ; i ++){
				jsonObj = (JSONObject)jsonArray.get(i);
				//list.add(jsonObj);
				list.add(getMapForJson(jsonObj.toString()));
			}
		} catch (Exception e) {
			// TODO: handle exception
			e.printStackTrace();
		}
		return list;
	}

	/**
	 * A方法追加文件：使用RandomAccessFile
	 */
	public static void appendMethodA(String fileName, String content) {
		try {
			// 打开一个随机访问文件流，按读写方式
			RandomAccessFile randomFile = new RandomAccessFile(fileName, "rw");
			// 文件长度，字节数
			long fileLength = randomFile.length();
			//将写文件指针移到文件尾。
			randomFile.seek(fileLength);
			randomFile.writeBytes(content);
			randomFile.close();
		} catch (IOException e) {
			e.printStackTrace();
		}
	}

	/**
	 * B方法追加文件：使用FileWriter
	 */
	public static void appendMethodB(String fileName, String content) {
		try {
			//打开一个写文件器，构造函数中的第二个参数true表示以追加形式写文件
			FileWriter writer = new FileWriter(fileName, true);
			writer.write(content);
			writer.close();
		} catch (IOException e) {
			e.printStackTrace();
		}
	}

	/**
	 * 图片旋转记录
	 */
	public void map2Json()
	{
		JSONArray jarr = new JSONArray();
		
		Map map = new HashMap();
		//for() {}
		jarr.put(90);	// rotate
		jarr.put(1);	// count
		map.put("001.jpg", jarr);
		jarr = new JSONArray();
		jarr.put(-90);	// rotate
		jarr.put(2);	// count
		map.put("002.jpg", jarr);

		JSONObject json = new JSONObject(map);
		System.out.println("map to json");
		System.out.println(json.toString());
	}

	public void json2o()
	{
		//1. List集合转换成json代码
		List list = new ArrayList();
		list.add( "first" );
		list.add( "second" );
		JSONArray jsonArray2 = new JSONArray(list);
		//jsonArray2.put( list );
		System.out.println("list to json");
		System.out.println(jsonArray2.toString());

		//2. Map集合转换成json代码
		Map map = new HashMap();
		map.put("name", "json");
		map.put("bool", Boolean.TRUE);
		map.put("int", new Integer(1));
		map.put("arr", new String[] { "a", "b" });
		JSONArray jarr = null;
		try {
			jarr = new JSONArray("[\"a\",\"b\",\"ddd\"]");
			map.put("arr2", jarr);
		} catch (Exception e) {
		}
		
		JSONArray jarr2 = new JSONArray();
		jarr2.put("abc");
		jarr2.put("edf");
		jarr2.put(jarr);
		map.put("arr3", jarr2);

		map.put("func", "function(i){ return this.arr[i]; }");
		JSONObject json = new JSONObject(map);
		System.out.println("map to json");
		System.out.println(json.toString());

		//3. Bean转换成json代码
		//JSONObject jsonObject = JSONObject.put(new JsonBean());
		//System.out.println("bean to json");
		//System.out.println(json.toString());

		//4. 数组转换成json代码
		boolean[] boolArray = new boolean[] { true, false, true };
		JSONArray jsonArray1 = new JSONArray().put(boolArray);
		System.out.println("array to json");
		System.out.println(jsonArray1.toString());

		//5. 一般数据转换成json代码
		JSONArray jsonArray3 = new JSONArray().put("['json','is','easy']" );
		System.out.println("string to json");
		System.out.println(jsonArray3.toString());

		System.out.println("json to object");
		try {
			JSONObject tmp = new JSONObject(jsonArray2.toString());
			System.out.println(tmp.toString());
		} catch (JSONException e) {
			System.out.println(e.toString());
		}
	}

	public static JSONObject getJSON4Map(Map map) {
		Iterator iter = map.entrySet().iterator();
		JSONObject holder = new JSONObject();
		while (iter.hasNext()) {
			Map.Entry pairs = (Map.Entry) iter.next();
			String key = (String) pairs.getKey();
			Map m = (Map) pairs.getValue();
			JSONObject data = new JSONObject();
			try {
				Iterator iter2 = m.entrySet().iterator();
				while (iter2.hasNext()) {
					Map.Entry pairs2 = (Map.Entry) iter2.next();
					data.put((String) pairs2.getKey(), 
							(String) pairs2.getValue());
				}
				holder.put(key, data);
			} catch (JSONException e) {
				System.out.println("Transforming: There was an error packaging JSON");
			}
		}
		return holder;
	}

	/**
	 * 遍历map
	 */
	public void listmap() 
	{
		Map<String, String> map = new HashMap<String, String>();
		map.put("1", "value1");
		map.put("2", "value2");
		map.put("3", "value3");
		
		//第一种：普遍使用，二次取值
		System.out.println("通过Map.keySet遍历key和value：");
		for (String key : map.keySet()) {
			System.out.println("key= "+ key + " and value= " + map.get(key));
		}

		//第一点五种：
		System.out.println("通过Map.keySet遍历key和value：");
		Set<String> key = map.keySet();
		for (Iterator it = key.iterator(); it.hasNext(); ) {
			String s = (String) it.next();
			System.out.println("key:"+s+" value:"+map.get(s));
		}

		//第二种
		System.out.println("通过Map.entrySet使用iterator遍历key和value：");
		Iterator<Map.Entry<String, String>> it = map.entrySet().iterator();
		while (it.hasNext()) {
			Map.Entry<String, String> entry = it.next();
			System.out.println("key= " + entry.getKey() + " and value= " + entry.getValue());
		}
		
		//第三种：推荐，尤其是容量大时
		System.out.println("通过Map.entrySet遍历key和value");
		for (Map.Entry<String, String> entry : map.entrySet()) {
			System.out.println("key= " + entry.getKey() + " and value= " + entry.getValue());
		}

		//第四种
		System.out.println("通过Map.values()遍历所有的value，但不能遍历key");
		for (String v : map.values()) {
			System.out.println("value= " + v);
		}
	}
}
