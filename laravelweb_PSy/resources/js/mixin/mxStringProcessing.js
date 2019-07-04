export default{
    methods:{
        strToPrice(angka,prefix)
        {
            //100000
            //9.000
            //11212
            //11.212
            angka = angka.toString();
            var hasil = "";
            var counter = 0;
            for(var i = angka.length - 1;i>= 0;i--)
            {
                counter++;
                if(counter % 3 == 0)
                {
                    if(i != 0)
                        hasil = "." + angka.charAt(i) +  hasil;
                }
                else
                {
                    hasil = angka.charAt(i) + hasil;
                }
            }
            return prefix + hasil;
        },
        firstUpper(str)
        {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }
    }
}