<template>
	<div style='background-color: white'>
		<div id='dashboard-top'>
			<v-layout row align-center>
				<v-flex>
					<img width=120 height=250 src='/assets/images/iconsatpam.png'></img>
				</v-flex>
				<v-flex>
					<h1> Welcome To Patrolee !</h1>
					<br>
					<h3>Current Events :</h3>
					<div id='list-current-events'>
						<p v-for='(item,index) in current_event'>{{index}} : {{item}}</p>
					</div>
				</v-flex>
				<v-spacer>
				</v-spacer>
				<v-flex>
					<v-layout row>
							
					</v-layout>
					<v-layout row>
						<div id='report-section-box'>
							<h2>Report Last Month : </h2>
							<div class='d-inline-block box-report'>
								<v-layout row align-center>
									<v-flex xs6>
										<h2 style='color:grey'>Secure</h2>
										<p class='percent-report'>{{secure[0]}}%</p>
									</v-flex>
									<v-flex xs6>
										<center>
											<img width=60 height=60 src='/assets/images/secureicon.png'></img>
										</center>
									</v-flex>
								</v-layout>
								<v-layout row >
									<v-flex>
										<span v-bind:style="{ color: secure[2] ? 'green' : 'red'}">{{secure[1]}}%</span>
										<v-icon v-if='secure[2]' style='line-height: 1.5' class='icontitledatatable' color="green darken-2" small>arrow_upward</v-icon>
										<v-icon v-if='!secure[2]' style='line-height: 1.5' class='icontitledatatable' color="red darken-2" small>arrow_downward</v-icon>
										<span style='color:grey'>Since {{secure[3]}}</span>
									</v-flex>
								</v-layout>

								
							</div>
							<div class='d-inline-block box-report'>
								<v-layout row align-center>
									<v-flex xs6>
										<h2 style='color:grey'>Presence</h2>
										<p class='percent-report'>{{presence[0]}}%</p>
									</v-flex>
									<v-flex xs6>
										<center>
											<img width=60 height=60 src='/assets/images/presenceicon.png'></img>
										</center>
									</v-flex>
								</v-layout>
								<v-layout row >
									<v-flex>
										<span v-bind:style="{ color: presence[2] ? 'green' : 'red'}">{{presence[1]}}%</span>
										<v-icon v-if='presence[2]' style='line-height: 1.5' class='icontitledatatable' color="green darken-2" small>arrow_upward</v-icon>
										<v-icon v-if='!presence[2]' style='line-height: 1.5' class='icontitledatatable' color="red darken-2" small>arrow_downward</v-icon>
										<span style='color:grey'>Since {{presence[3]}}</span>
									</v-flex>
								</v-layout>

								
							</div>
						</div>
					</v-layout>
				</v-flex>
			</v-layout>
		</div>

		
		<center><p style='margin-top:40px;font-size: 40px;font-weight: bold'>Overview</p></center>
		<br>
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
				current_event : {
					'Gedung Agape' : 'Ada acara IAA',
					'Gedung Didaktos' : 'Tidak ada apa-apa',
					'Gedung Logos' : 'Tidak ada apa-apa',
					'Gedung Makarios' : 'Tidak ada apa-apa',
					'Depan ATM' : 'Tidak ada apa-apa',
				},
				secure : ['50', '40', 0, 'February'],
				presence : ['98', '5', 1, 'February'],
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
	                
	                
	                if(r.message == "Your are not admin")
	                {
	                    this.$router.replace('/login');
	                }
	            	else
	                {
	                	//isi smallreport
	                	var r_report = r.data.smallReportData;


	                	//- isi current event
	                	this.current_event = {};
	                	for(var  i = r_report.currentEvent.length - 1;i>=0;i--)
	                	{
	                		this.current_event[r_report.currentEvent[i].room.name] = r_report.currentEvent[i].message;
	                	}
	                	console.log('cek this');
	                	console.log(this.current_event);

	                	//- isi percentage
	     //            	secure : ['50', '40', 0, 'February'],
						// presence : ['98', '5', 1, 'February'],
						this.secure[0] = r_report.securePercentageThisMonth;
						this.secure[1] = Math.abs(r_report.differentSecureBetweenMonth);
						if(r_report.differentSecureBetweenMonth < 0)
						{
							this.secure[2] = 0;
						}
						else
						{
							this.secure[2] = 1;
						}
						this.secure[3] = 'Last Month';

						this.presence[0] = r_report.presencePercentageThisMonth;
						this.presence[1] = Math.abs(r_report.differentPresenceBetweenMonth);
						if(r_report.differentPresenceBetweenMonth < 0)
						{
							this.presence[2] = 0;
						}
						else
						{
							this.presence[2] = 1;
						}
						this.presence[3] = 'Last Month';


	                	//isi graph
	                	var r_graph = r.data.graphData;
	                	var temp_push = [];
		                temp_push.push(this.convert_month(r_graph[0].month));
		                for(var i = 0;i<r_graph.length;i++)
		                {
		                	if(r_graph[i].status_nodes == 'Mencurigakan')
		                	{
		                		temp_push[1] = parseInt(r_graph[i].count);
		                	}
		                	else if(r_graph[i].status_nodes == 'Tidak Aman')
		                	{
		                		temp_push[2] = parseInt(r_graph[i].count);
		                	}
		                	else if(r_graph[i].status_nodes == 'Aman')
		                	{
		                		temp_push[3] = parseInt(r_graph[i].count);
		                	}
		                	for(var j = 1;j<=3;j++)
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