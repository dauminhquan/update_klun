<template>
    <div class="page-content">

        <!-- Main sidebar -->
        <div class="sidebar sidebar-main sidebar-default sidebar-separate">
            <div class="sidebar-content">

                <!-- Sidebar search -->
                <div class="panel panel-white">
                    <div class="panel-heading">
                        <div class="panel-title text-semibold">
                            <i class="icon-search4 text-size-base position-left"></i>
                            Lọc dữ liệu
                        </div>
                    </div>

                    <div class="panel-body">
                        <div>
                            <div class="form-group">
                                <div class="has-feedback has-feedback-left">
                                    <input type="search" v-model="search" class="form-control" placeholder="Từ khóa tìm kiếm">
                                    <div class="form-control-feedback">
                                        <i class="icon-cog6 text-size-large text-muted"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <check-box v-for="type in types" :key="type.id" :text="type.name" :value="type.id" @checked="searchType($event)"></check-box>
                            </div>

                            <button type="button" @click="getTasks()" class="btn bg-blue btn-block">
                                <i class="icon-search4 text-size-base position-left"></i>
                                Tìm việc
                            </button>
                        </div>
                    </div>
                </div>

                <div class="panel panel-white">
                    <div class="panel-heading">
                        <div class="panel-title text-semibold">
                            <i class="icon-menu7 position-left"></i>
                            Vị trí
                        </div>
                    </div>

                    <form action="#">
                        <div class="panel-body">
                            <v-select :options="positions"  v-model="positionsSelect" label="name" :multiple="true"></v-select>
                        </div>

                    </form>
                </div>

                <div class="panel panel-white">
                    <div class="panel-heading">
                        <div class="panel-title text-semibold">
                            <i class="icon-collaboration position-left"></i>
                            Kỹ năng
                        </div>
                    </div>

                    <form action="#">
                        <div class="panel-body">
                            <v-select :options="skills" label="name" v-model="skillsSelect" :multiple="true"></v-select>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        <!-- /main sidebar -->


        <!-- Main content -->
        <div class="content-wrapper">

            <!-- Cards layout -->
            <div class="panel panel-white">
                <div class="panel-heading">
                    <h6 class="panel-title text-semibold">Danh sách việc làm : <span v-html="typeText"></span>
                        <span v-html="skillText"></span>
                        <span v-html="positionText"></span>
                    </h6>
                </div>
                <div class="pace-demo" v-if="searching == true">
                    <div class="theme_xbox_xs"><div class="pace_progress" data-progress-text="60%" data-progress="60"></div><div class="pace_activity"></div></div>
                </div>
                <ul class="media-list">
                    <li class="media panel-body stack-media-on-mobile" v-for="task in tasks" :key="task.id">
                        <div class="media-left">
                            <a href="#">
                                <img :src="task.enterprise.avatar" class="img-rounded img-lg" alt="">
                            </a>
                        </div>

                        <div class="media-body">
                            <h6 class="media-heading text-semibold">
                                <a :href="getUrlTask(task.id)">{{task.title}}</a>
                            </h6>

                            <ul class="list-inline list-inline-separate text-muted mb-10">
                                <li v-for="skill in task.skills" :key="skill.id">{{skill.name}}</li>
                                <li v-for="type in task.types" :key="type.id">{{type.name}}</li>
                                <li v-for="position in task.positions" :key="position.name">{{position.name}}</li>
                            </ul>

                            {{task.description}}
                        </div>
                    </li>
                </ul>
            </div>
            <!-- /cards layout -->


            <!-- Pagination -->
            <div class="text-center content-group-lg pt-20">
                <ul class="pagination">
                    <li v-if="currentPage > 1" @click="currentPage--"><a :href="null"><i class="icon-arrow-small-left"></i></a></li>
                    <li v-for="inPage in listPage" :key="inPage" @click="currentPage = inPage"><a :href="null">{{inPage}}</a></li>
                    <li v-if="currentPage < totalPage" @click="currentPage++"><a :href="null"><i class="icon-arrow-small-right"></i></a></li>
                </ul>
            </div>
            <!-- /pagination -->

        </div>
        <!-- /main content -->

    </div>
</template>

<script>
    import checkBox from './components/checkbox'
    import axios from './../../axios'
    import config from './../../config'
    import vSelect from 'vue-select'
    export default {
        components: {
            'check-box' : checkBox,
            'v-select': vSelect
        },
        watch:{
          currentPage(value){
              this.getData()
          }
        },
        computed: {
          listPage(){
              let vm  = this
              let pages = []
                  for(let i = 1;i<vm.totalPage;i++)
                  {
                      pages.push(i)
                  }
                  return pages
          },
            typeText(){
              let vm = this
                let text = ''
                vm.typesSelect.forEach(item => {
                    let type = vm.types.find(vl => {
                        return vl.id == item
                    })
                    if(type != undefined)
                    {
                        text+= '<mark class="bg-primary">'+type.name+'</mark> '
                    }
                })
                return text
            },
            skillText(){
                let vm = this
                let text = ''
                vm.skillsSelect.forEach(item => {
                    text+= '<mark class="bg-primary">'+item.name+'</mark> '
                })
                return text
            },
            positionText(){
                let vm = this
                let text = ''
                vm.positionsSelect.forEach(item => {
                    text+= '<mark class="bg-slate">'+item.name+'</mark> '
                })
                return text
            }
        },
        data(){
            return {
                types: [],
                skills:[],
                positions:[],
                typesSelect: [],
                skillsSelect: [],
                positionsSelect: [],
                pageType: 1,
                tasks: [],
                search: '',
                config: new config(),
                currentPage : 1,
                totalPage: null,
                searching:false
            }
        },
        methods:{
            searchType(arg)
            {
                let vm = this
               if(arg[1] == null)
               {
                   let index = vm.typesSelect.indexOf(arg[0])
                   console.log(index)
                   if(index != -1)
                   {
                       vm.typesSelect.splice(index,1)
                   }
               }
               else{
                    vm.typesSelect.push(arg[0])
               }
            },
            getTypes(){
                let vm = this
                axios.get(vm.config.API_TYPES+'?size=-1').then(data => {
                    vm.types = data.data.data
                }).catch(err => {
                    console.dir(err)
                    vm.config.notifyError('Đã có lỗi từ server')
                })
            },
            getSkills(){
                let vm = this
                axios.get(vm.config.API_SKILLS+'?size=-1').then(data => {
                    vm.skills = data.data.data
                }).catch(err => {
                    console.dir(err)
                    vm.config.notifyError('Đã có lỗi từ server')
                })
            },
            getPositions(){
                let vm = this
                axios.get(vm.config.API_POSITIONS+'?size=-1').then(data => {
                    vm.positions = data.data.data
                }).catch(err => {
                    console.dir(err)
                    vm.config.notifyError('Đã có lỗi từ server')
                })
            },
            getTasks(){

                let vm = this
                vm.tasks = []
                vm.searching = true
                let skills = []
                let positions = []
                if(vm.skillsSelect.length != 0)
                {
                    vm.skillsSelect.forEach(item => {
                        skills.push(item.id)
                    })
                }
                if(vm.positionsSelect.length != 0)
                {
                    vm.positionsSelect.forEach(item => {
                        positions.push(item.id)
                    })
                }
                axios.get(vm.config.API_TASKS,{
                    params:{
                        types: vm.typesSelect,
                        skills:skills,
                        positions:positions,
                        search:vm.search
                    }
                }).then(data => {
                    vm.tasks = data.data.data
                    vm.totalPage = data.data.last_page
                    vm.currentPage = data.data.current_page
                    vm.searching = false
                }).catch(err => {
                    console.dir(err)
                    vm.searching = false

                    vm.config.notifyError('Không thể lấy các tin tuyển dụng')
                })
            },
            getUrlTask(id)
            {
                return this.config.WEB_TASKS+'/'+id
            }
        },
        mounted(){
            let vm = this
            vm.getTypes()
            vm.getSkills()
            vm.getPositions()
            vm.getTasks()
        }
    }

</script>
