
<template>
    <fullscreen ref="fullscreen" @change="fullscreenChange">
        <v-app>
            <v-toolbar app dense fixed clipped-left color="menu" dark>
                <v-toolbar-side-icon
                @click.stop="drawer = !drawer"
                ></v-toolbar-side-icon>
                <v-spacer></v-spacer>

                

                <!-- fullscreen button -->
                <v-tooltip bottom>
                    <template slot="activator">
                        <v-btn icon @click="toggleFullscreen">
                            <v-icon v-if="fullscreen == false">fullscreen</v-icon>
                            <v-icon v-else>fullscreen_exit</v-icon>
                        </v-btn>
                    </template>
                    <span v-if="fullscreen == false">Enter Fullscreen Mode</span>
                    <span v-else>Exit Fullscreen Mode</span>
                </v-tooltip>

                 <!-- Profil button -->
                <v-menu open-on-hover offset-y offset-x>
                    <v-btn
                        class="text-none ma-0"
                        slot="activator"
                        depressed
                        flat
                        >
                        <span class="subheading text-xs-center overflow-text">
                            Halo,
                            <span class="font-weight-medium">
                                {{ user.name }}
                            </span>
                        </span>
                    </v-btn>

                    <v-list>
                        <v-list-tile
                            v-for="(item, index) in toolbarMenu"
                            :key="index"
                            :to="item.action"
                            >
                            <v-list-tile-action>
                                <v-icon>{{ item.icon }}</v-icon>
                            </v-list-tile-action>
                            <v-list-tile-content>
                                <v-list-tile-title>{{ item.title }}</v-list-tile-title>
                            </v-list-tile-content>
                        </v-list-tile>
                    </v-list>
                </v-menu>
            </v-toolbar>

            <v-navigation-drawer
                v-model="drawer"
                app clipped fixed
                :width='270'

            >

                <!-- PROFIL PERUSAHAAN -->

                <v-container fluid>
                    <v-layout row>
                        <v-flex xs3>
                            <v-avatar color="grey lighten-4">
                                <img src="/assets/images/logo.png"></img>
                            </v-avatar>
                        </v-flex>
                        <v-flex xs9 class='ml15'>
                            <div class='title black--text'>Patrolee</div>
                            <div class='subheading black--text'>Sistem Keamanan</div>
                        </v-flex>
                    </v-layout>
                </v-container>

                <v-divider class='white'></v-divider>


                <v-list expand>
                    <!--  -->
                    <div v-for="(item,index) in routes">


                        <v-list-group
                            value='true'
                            v-if="item.subroutes"
                            router
                            :key="'menu'+index"
                            
                            
                            no-action
                            >
                            <template v-slot:activator>
                                <v-list-tile >
                                    <v-list-tile-action class='ltc-icon' >
                                        <v-icon class='color-text-sidebar icon-sidebar'>{{ item.icon }}</v-icon>
                                    </v-list-tile-action>
                                    <v-list-tile-content >
                                        <v-list-tile-title class='color-text-sidebar ff-text-sidebar'>{{ item.title }}</v-list-tile-title>
                                    </v-list-tile-content>
                                </v-list-tile>
                            </template>

                            <v-list-tile

                                v-for="subItem in item.subroutes"
                                :key="subItem.subtitle"
                                :to="subItem.subaction"
                                class='sub-menu'
                                
                                
                                @click=""
                            >
                                <v-list-tile-action class='ltc-icon'>
                                    <v-icon class='color-text-sidebar icon-sidebar'>{{ subItem.subicon }}</v-icon>
                                </v-list-tile-action>
                                <v-list-tile-content>
                                    <v-list-tile-title class='color-text-sidebar ff-text-sidebar'>{{ subItem.subtitle }}</v-list-tile-title>
                                </v-list-tile-content>


                            </v-list-tile>
                        </v-list-group>


                        <v-list-tile
                            v-else
                            router
                            :to="item.action"
                            :key="'menu'+index"

                            >
                            <v-list-tile-action class='ltc-icon'>
                                <v-icon class='color-text-sidebar icon-sidebar'>{{ item.icon }}</v-icon>
                            </v-list-tile-action>

                            <v-list-tile-content>
                                <v-list-tile-title class='color-text-sidebar ff-text-sidebar'>{{ item.title }}</v-list-tile-title>
                            </v-list-tile-content>
                        </v-list-tile>

                    </div>
                </v-list>
            </v-navigation-drawer>

            <v-content>
                <router-view></router-view>
            </v-content>
        </v-app>
    </fullscreen>
</template>
<style>

.ml15
{
    margin-left: 15px !important;
}
.sub-menu a
{
    padding-left: 45px !important;
}

.ltc-icon
{
    min-width: 36px !important;
    margin-left: 10px !important;
}
.color-text-sidebar
{
    color:#474747;
    text-decoration: none;
    font-size: 14px;

}
.active-menu
{
    color:red !important;
}
.icon-sidebar
{
    font-size: 20px !important;
    color:#474747 !important;
}
.ff-text-sidebar
{
    font-family: 'Open Sans',sans-serif;
}
</style>
<script>

export default {
    data() {
        return {
            fullscreen: false,
            user: {},
            drawer: true,
            routes: [
                {
                    icon: "dashboard",
                    title: "Dashboard",
                    action:"/",

                },
                {
                    icon: "build",
                    title: "IoT",
                    action:"/iot",

                },
                {
                    icon: "store",
                    title: "Master Data",
                    subroutes:[
                        {
                            subicon:"domain",
                            subtitle:"Gedung",
                            subaction: "/building" 
                        },
                        {
                            subicon:"list",
                            subtitle:"Lantai",
                            subaction: "/floor"
                        },
                        {
                            subicon:"surround_sound",
                            subtitle:"Gateway",
                            subaction: "/gateway" 
                        },
                        {
                            subicon:"meeting_room",
                            subtitle:"Ruangan",
                            subaction: "/room"
                        },
                        {
                            subicon:"access_time",
                            subtitle:"Waktu Jaga",
                            subaction: "/time"
                        },
                        {
                            subicon:"radio_button_checked",
                            subtitle:"Kondisi Lokasi",
                            subaction: "/statusnode"
                        },
                        {
                            subicon:"person",
                            subtitle:"Satpam",
                            subaction: "/user"
                        },
                    ]
                },
                {
                    icon: "local_convenience_store",
                    title: "Jadwal Hari Ini",
                    action:"/shifttoday",

                },
                {
                    icon: "report",
                    title: "Laporan",
                    action:"/report",

                },
            ],
            toolbarMenu: [
                {
                    icon: "exit_to_app",
                    title: "Keluar",
                    action: "/logout"
                }
            ],
            
        }
    },
    methods: {
        toggleFullscreen () {
            this.$refs['fullscreen'].toggle()
        },
        fullscreenChange(fullscreen) {
            this.fullscreen = fullscreen
        },
    },
    mounted() {
        this.user = JSON.parse(localStorage.getItem('user'))
       
    }
}
</script>
