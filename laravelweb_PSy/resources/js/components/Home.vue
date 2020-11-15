<template>
	<div style='background-color: white'>
		<div id='dashboard-top'>
			<v-layout row align-center>
				<v-flex>
					<center>
						<img width=80 height=160 src='/assets/images/iconsatpam.png' style='margin-left:-80px'></img>
					</center>
				</v-flex>
				<v-flex>
					<h1 style='font-size:34px'> Selamat Datang !</h1>
					<br>
					<h3 style='font-size:20px'>Kondisi terkini :</h3>
					<div style='font-size:15px' id='list-current-events'>
						<p v-for='(item,index) in current_event'>{{index}} : {{item}}</p>
					</div>
				</v-flex>
				<v-spacer>
				</v-spacer>
				<!-- <v-flex>
					<v-layout row>
							
					</v-layout>
					<v-layout row>
						<div id='report-section-box'>
							<h2>Laporan Bulan Lalu : </h2>
							<div class='d-inline-block box-report'>
								<v-layout row align-center>
									<v-flex xs6>
										<h2 style='color:grey'>Aman</h2>
										<p class='percent-report'>{{secure[0]}}%</p>
									</v-flex>
									<v-flex xs6>
										<center style='padding-top:10px'>
											<img width=60 height=60 src='/assets/images/secureicon.png'></img>
										</center>
									</v-flex>
								</v-layout>
								<v-layout row >
									<v-flex>
										<span v-bind:style="{ color: secure[2] ? 'green' : 'red'}">{{secure[1]}}%</span>
										<v-icon v-if='secure[2]' style='line-height: 1.5' class='icontitledatatable' color="green darken-2" small>arrow_upward</v-icon>
										<v-icon v-if='!secure[2]' style='line-height: 1.5' class='icontitledatatable' color="red darken-2" small>arrow_downward</v-icon>
										<span style='color:grey'>Sejak {{secure[3]}}</span>
									</v-flex>
								</v-layout>

								
							</div>
							<div class='d-inline-block box-report'>
								<v-layout row align-center>
									<v-flex xs6>
										<h2 style='color:grey'>Presensi</h2>
										<p class='percent-report'>{{presence[0]}}%</p>
									</v-flex>
									<v-flex xs6>
										<center style='padding-top:10px'>
											<img width=60 height=60 src='/assets/images/presenceicon.png'></img>
										</center>
									</v-flex>
								</v-layout>
								<v-layout row >
									<v-flex>
										<span v-bind:style="{ color: presence[2] ? 'green' : 'red'}">{{presence[1]}}%</span>
										<v-icon v-if='presence[2]' style='line-height: 1.5' class='icontitledatatable' color="green darken-2" small>arrow_upward</v-icon>
										<v-icon v-if='!presence[2]' style='line-height: 1.5' class='icontitledatatable' color="red darken-2" small>arrow_downward</v-icon>
										<span style='color:grey'>Sejak {{presence[3]}}</span>
									</v-flex>
								</v-layout>

								
							</div>
						</div>
					</v-layout>
				</v-flex> -->
			</v-layout>
		</div>

		
		<center><p style='margin-top:40px;font-size: 40px;font-weight: bold'>Dashboard</p></center>
		<br>
		<div style='overflow-x: auto;overflow-y: hidden;'>
			<center><h3>Laporan tahun ini : {{total_event}}</h3></center>
			<GChart
			v-if='chartData[1]'
		    type="BarChart"
		    :data="chartData"
		    :options="chartOptions"
		    style='height:1000px;'
		  	/>

		</div>

	</div>
</template>
<script>
	import { GChart } from 'vue-google-charts'
	import mxCrud from './../mixin/mxCrud'

	export default {
		components: {
		    GChart
		},
		mixins:[
			mxCrud
		],
		data () {
			return {
				
				current_event : {
					'Gedung Agape' : 'Ada acara IAA',
					'Gedung Didaktos' : 'Tidak ada apa-apa',
					'Gedung Logos' : 'Tidak ada apa-apa',
					'Gedung Makarios' : 'Tidak ada apa-apa',
					'Depan ATM' : 'Tidak ada apa-apa',
				},
				chartData: [
		        	['Month', 'Aman', 'Mencurigakan', 'Tidak Aman'],
		       
		      	],
		      	chartOptions: {
		      		width:'1200',
		      		chartArea:{left:100,top:50,bottom:80,width:"800"},
		        	chart: {
		          		height:1500,
		          		width:600,
		          		
		        	},
		        	colors: ['#40ff4d', '#ffa040', '#ffe940', '#40ffc6', '#4059ff'],
		        	hAxis: {
		        		format: '0',
		        		title: 'The Number of events'
		        	}
    
		      	}
			}
		},
		computed : 
		{
			total_event : function()
			{
				console.log(this.chartData);
				var total = 0;
				for(var i = 1;i<this.chartData.length;i++)
				{
					total += (parseInt(this.chartData[i][1]) + parseInt(this.chartData[i][2]) + parseInt(this.chartData[i][3]));
				}
				return total;
			}
		},
		methods : 
		{
			pad(n){return n<10 ? '0'+n : n},
			convert_month(str)
			{
				if(str == '01')
				{
					return 'Januari';
				}
				else if(str == '02')
				{
					return 'Februari';
				}
				else if(str == '03')
				{
					return 'Maret';
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
					return 'Juni';
				}
				else if(str == '07')
				{
					return 'Juli';
				}
				else if(str == '08')
				{
					return 'Agustus';
				}
				else if(str == '09')
				{
					return 'September';
				}
				else if(str == '10')
				{
					return 'Oktober';
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
	                
	                
	                if(r.message == "Your are not admin")
	                {
						localStorage.setItem("token", null);
                        localStorage.setItem("user", null);
						//next({ path: '/login', replace: true});
	                    this.$router.push({ path: '/login'}).catch((err) => {
                            console.log('ada error');
                            console.log(err);
                        });
	                }
	            	else
	                {
	                	//isi smallreport
	                	var r_report = r.data.smallReportData;


	                	//- isi current event
	                	this.current_event = {};
	                	for(var  i = 0;i<r_report.currentEvent.length;i++)
	                	{
	                		this.current_event[r_report.currentEvent[i].shift.room.name] = r_report.currentEvent[i].message;
	                	}
	                	console.log('cek current event');
	                	console.log(this.current_event);


	                	//isi graph
	                	var r_graph = r.data.graphData;
	                	//isi semua bulan, karena ada kemungkinan dari backend ada bulan yang tidak dikembalikan jika tidak ada datanya
	                	//r_graph : [{month : 1}, {month : 1}, {month : 2}, {month : 3}, {month : 3}, {month : 5}, {month : 7}, {month : 8}, {month : 8}]
	                	//i : 1 - 12

	                	var idxcek = 0;
	                	for(var i = 1;i<13;i++)
	                	{
	                		var count = 0;
	                		for(var j = 0;j<r_graph.length;j++)
	                		{
	                			if(parseInt(r_graph[j].month) == i)
	                			{
	                				count += 1;
	                			}
	                		}
	                		if(count == 0)
	                		{
	                			for(var j = 0;j<r_graph.length;j++)
	                			{
	                				if(parseInt(r_graph[j].month) > i)
	                				{
	                					r_graph.splice(j,0,{month:i});		
	                					break;
	                				}
	                			}
	                			
	                		}
	                	}
						
						var temp_status_node = [];
						for(var i = 0;i<r.data.statusNodeData.length;i++)
						{
							this.chartData[0][i + 1] = r.data.statusNodeData[i].name;
							temp_status_node.push(r.data.statusNodeData[i]);
						}

	                	console.log('cek r_graph');
	                	console.log(r_graph);
	                	var temp_push = [];
		                temp_push.push(this.convert_month(r_graph[0].month));
		                for(var i = 0;i<r_graph.length;i++)
		                {
							//search r_graph[i].status_nodes_id in temp_status_node, and get the index of that
							//example : 
							//temp_status_node : 
							//[0] : {id:1,name:"aman"}
							//[1] : {id:2,name:"mencurigakan"}
							//[2] : {id:3,name:"tidak aman"}
							//[3] : {id:4,name:"sejuk"}
							//r_graph[i].status_nodes_id = 4
							//then index_in_temp_status_node is 3

							var index_in_temp_status_node = -1;
							console.log('cek dulu nih keknya adayg salah');
							console.log(r_graph);
							console.log('===');
							console.log(temp_status_node);
							for(var j = 0;j<temp_status_node.length;j++)
							{
								if(parseInt(r_graph[i].status_nodes_id) == temp_status_node[j].id)
								{
									index_in_temp_status_node = j;
									break;
								}
							}

							
							//put in order
							//example : 
							//index_in_temp_status_node : 3
							//then, put temp_push[3 + 1] = parseInt(r_graph[i].count)
							//because, temp_push[0] is name of month like "january" or "february" or ...
							if(index_in_temp_status_node != -1)
							{
								temp_push[index_in_temp_status_node +  1] = parseInt(r_graph[i].count);
							}
							
							//fill 0 in temp_push if there is null data 
		                	for(var j = 1;j<=temp_status_node.length;j++)
		                	{
		                		if(!temp_push[j])
		                			temp_push[j] = 0;
							}
							

		                	if(i != r_graph.length - 1 && r_graph[i + 1].month != r_graph[i].month)
		                	{
		                		this.chartData.push(temp_push);
		                		temp_push = [];
								temp_push.push(this.convert_month(r_graph[i + 1].month));
		                	}
		                	else if(i == r_graph.length - 1)
		                	{
		                		this.chartData.push(temp_push);
		                	}
						}
						console.log('cek chartdata');
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