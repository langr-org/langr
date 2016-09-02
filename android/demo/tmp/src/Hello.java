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
	 * ����map
	 */
	public void listmap() 
	{
		Map<String, String> map = new HashMap<String, String>();
		map.put("1", "value1");
		map.put("2", "value2");
		map.put("3", "value3");
		
		//��һ�֣��ձ�ʹ�ã�����ȡֵ
		System.out.println("ͨ��Map.keySet����key��value��");
		for (String key : map.keySet()) {
			System.out.println("key= "+ key + " and value= " + map.get(key));
		}

		//��һ�����֣�
		System.out.println("ͨ��Map.keySet����key��value��");
		Set<String> key = map.keySet();
		for (Iterator it = key.iterator(); it.hasNext(); ) {
			String s = (String) it.next();
			System.out.println("key:"+s+" value:"+map.get(s));
		}

		//�ڶ���
		System.out.println("ͨ��Map.entrySetʹ��iterator����key��value��");
		Iterator<Map.Entry<String, String>> it = map.entrySet().iterator();
		while (it.hasNext()) {
			Map.Entry<String, String> entry = it.next();
			System.out.println("key= " + entry.getKey() + " and value= " + entry.getValue());
		}
		
		//�����֣��Ƽ���������������ʱ
		System.out.println("ͨ��Map.entrySet����key��value");
		for (Map.Entry<String, String> entry : map.entrySet()) {
			System.out.println("key= " + entry.getKey() + " and value= " + entry.getValue());
		}

		//������
		System.out.println("ͨ��Map.values()�������е�value�������ܱ���key");
		for (String v : map.values()) {
			System.out.println("value= " + v);
		}
	}
}
