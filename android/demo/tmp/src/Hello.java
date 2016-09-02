import java.util.*;
/**
 * array-array: [[time,n,s],[time,n,s],[time,n,s]]
 * array-map: [{"time":time,"n":n,"s":s},{"time":time,"n":n,"s":s},{"time":time,"n":n,"s":s}]
 * map-array: {"id1":[time,n,s],"id2":[time,n,s],"id3":[time,n,s]}
 * map-map: {"id1":{"time":time,"n":n,"s":s},"id2":{"time":time,"n":n,"s":s},"id3":{"time":time,"n":n,"s":s}}
 *
 * map-array: {"filename":[90,1],"filename2":[90,3],"filename3":[90,1]}
 * map-map: {"filename":{"rote":90,"count":1},"filename2":{"rote":90,"count":3},"filename3":{"rote":90,"count":1}}
 */
public class Hello
{
	public static void main(String []args) 
	{
		System.out.println("Hello");
		Hello t = new Hello();
		//t.json2o();
		t.listmap();
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
