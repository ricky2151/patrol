<template>
	<div style='background-color: white'>
		<br>
		<center><h1>Dashboard</h1></center>
		<GChart
		v-if='chartData[1]'
	    type="BarChart"
	    :data="chartData"
	    :options="chartOptions"
	    style='height:1000px'
	  />

	</div>
</template>
<script>
	import { GChart } from 'vue-google-charts'

	export default {
		components: {
		    GChart
		  },
		data () {
			return {
				chartData: [
		        ['Month', 'Mencurigakan', 'Tidak Aman', 'Aman'],
		       
		      ],
		      chartOptions: {
		      	width:'2000',
		      	chartArea:{left:100,top:50,width:"800",height:'100%'},
		        chart: {
		          title: 'Company Performance',
		          subtitle: 'Sales, Expenses, and Profit: 2014-2017',
		          height:1000,
		          width:1000,

		        }
		      }
			}
		},
		methods : 
		{
			convert_month(str)
			{
				if(str == '01')
				{
					return 'January';
				}
				else if(str == '02')
				{
					return 'February';
				}
				else if(str == '03')
				{
					return 'March';
				}
				else if(str == '04')
				{
					return 'April';
				}
				else if(str == '05')
				{
					return 'Mei';
				}
				else if(str == '06')
				{
					return 'Juny';
				}
				else if(str == '07')
				{
					return 'July';
				}
				else if(str == '08')
				{
					return 'August';
				}
				else if(str == '09')
				{
					return 'September';
				}
				else if(str == '10')
				{
					return 'October';
				}
				else if(str == '11')
				{
					return 'November';
				}
				else if(str == '12')
				{
					return 'Desember';
				}
			},
			get_data() {

	            axios.get('/api/admin/shifts/graph', {
	                params:{
	                    token: localStorage.getItem('token')
	                }
	            },this.header_api).then((r) => {
	                r = r.data;
	                console.log('test r');
	                console.log(r);
	                
	                if(r.message == "Your are not admin")
	                {
	                    this.$router.replace('/login');
	                }
	            	else
	                {
	                	r = r.data;
	                	var temp_push = [];
		                temp_push.push(this.convert_month(r[0].month));
		                for(var i = 0;i<r.length;i++)
		                {
		                	if(r[i].status_nodes == 'Mencurigakan')
		                	{
		                		temp_push[1] = parseInt(r[i].count);
		                	}
		                	else if(r[i].status_nodes == 'Tidak Aman')
		                	{
		                		temp_push[2] = parseInt(r[i].count);
		                	}
		                	else if(r[i].status_nodes == 'Aman')
		                	{
		                		temp_push[3] = parseInt(r[i].count);
		                	}
		                	for(var j = 1;j<=3;j++)
		                	{
		                		if(!temp_push[j])
		                			temp_push[j] = 0;
		                	}
		                	if(i != r.length - 1 && r[i + 1].month != r[i].month)
		                	{
		                		this.chartData.push(temp_push);
		                		temp_push = [];
		                		temp_push.push(this.convert_month(r[i + 1].month));
		                	}
		                }
		                console.log(this.chartData);
	                }
	            });
	        },
		},
		mounted()
		{
			this.get_data();
		}
	}
</script>