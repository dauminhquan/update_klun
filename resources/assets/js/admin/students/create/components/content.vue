<template>
    <div class="content-wrapper">
        <form v-on:submit.prevent="submitInsert">
            <div class="panel panel-flat">
                <div class="panel-body">
                    <div class="row">
                        <fieldset>
                            <legend class="text-semibold"><i class="icon-reading position-left"></i>Điền thông tin sinh viên</legend>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Mã sinh viên:</label>
                                        <input type="text" v-model="info.code" placeholder="Mã sinh viên" required class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tên đầy đủ:</label>
                                        <input type="text" placeholder="Tên đầy đủ" v-model="info.full_name" required class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Họ và tên đệm</label>
                                        <input type="text" placeholder="Họ và tên đệm" required v-model="info.first_name" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Tên</label>
                                        <input type="text" v-model="info.last_name" required placeholder="Tên sinh viên" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Giới tính</label>
                                        <select name="" id="" class="form-control" v-model="info.sex">
                                            <option :value="1">Nam</option>
                                            <option :value="0">Nữ</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Ngày tháng năm sinh:</label>
                                        <input type="date" v-model="info.birth_day" required placeholder="Ngày tháng năm sinh" class="form-control">
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Khoa học</label>
                                        <v-select :options="departments" label="name" v-model="departmentSelect" required="true"></v-select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Khóa học</label>
                                        <v-select :options="courses" :disabled="departmentSelect == null" label="name" v-model="info.course_code" required="true"></v-select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Ngành học</label>
                                        <v-select :options="branches" :disabled="departmentSelect == null" label="name" v-model="info.branch_code" required="true"></v-select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Tên lớp</label>
                                        <input type="text" v-model="info.main_class" required placeholder="Tên lớp" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Địa chỉ Email:</label>
                                        <input type="email" v-model="info.email_address" required placeholder="Email" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Số điện thoại #:</label>
                                        <input type="tel" v-model="info.phone_number" required placeholder="+84" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Địa chỉ sinh sống:</label>
                                        <input type="tel" v-model="info.address" required placeholder="Địa chỉ sinh sống" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Thành phố:</label>
                                        <v-select :options="provinces" label="name" v-model="info.province_id" required="true"></v-select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div :class="styleGraduated">
                                    <div class="form-group">
                                        <label>Tình trạng tốt nghiệp:</label>
                                        <select v-model="info.graduated" class="form-control">
                                            <option value="0" selected>Chưa tốt nghiệp</option>
                                            <option value="1">Đã tốt nghiệp</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3"  v-if="info.graduated ==1 ">
                                    <div class="form-group">
                                        <label>Ngày tốt nghiệp:</label>
                                        <input type="date" v-model="info.date_graduated" required placeholder="Thời gian tốt nghiệp" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3"  v-if="info.graduated ==1 ">
                                    <div class="form-group">
                                        <label>Tốt nghiệp hạng:</label>
                                        <v-select :options="ratings" label="name"  v-model="info.rating_id"></v-select>
                                    </div>
                                </div>
                                <div class="col-md-3"  v-if="info.graduated ==1 ">
                                    <div class="form-group">
                                        <label>Điểm trung bình môn khi tốt nghiệp:</label>
                                        <input type="number" v-model="info.medium_score" min="5" max="10" required placeholder="Điểm trung bình" step="0.001" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Điền thông tin về sinh viên</label>
                                <textarea id="textarea-info" rows="5" cols="5" class="form-control" v-model="info.introduce" placeholder="Điền thông tin thêm về sinh viên"></textarea>
                            </div>
                        </fieldset>
                    </div>
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">Thêm mới sinh viên <i class="icon-arrow-right14 position-right"></i></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>
<script>
    import vSelect from 'vue-select'
    import axios from './../../../../axios'
    import config from './../../../../config'
    export default {
        components: {
            'v-select': vSelect
        },
        computed: {
            departmentSelectId(){
                let vm = this
                if(vm.departmentSelect == null)
                {
                    return null
                }
                return vm.departmentSelect.code
            },
            styleGraduated(){
                let vm = this
                if(vm.info.graduated == 1)
                {
                    return 'col-md-3'
                }
                return 'col-md-12'
            }
        },
        watch:{
            departmentSelectId(value){
                let vm = this
                if(value != null)
                {
                    vm.getBranches(value)
                }
                else{
                    vm.branches = []
                }
            }
        },
        data(){
            return {
                info:{
                    "code": null,
                    "first_name": null,
                    "last_name": null,
                    "full_name": null,
                    "address": null,
                    "sex": null,
                    "phone_number": null,
                    "email_address": null,
                    "birth_day": null,
                    "province_id": null,
                    "rating_id": null,
                    "introduce": null,
                    "graduated": null,
                    "medium_score": null,
                    "date_graduated": null,
                    "avatar":null,
                    "course_code": null,
                    "branch_code": null,
                    "main_class": null,
                },
                provinces: [],
                ratings: [],
                courses: [],
                branches: [],
                departments: [],
                departmentSelect: null,
                config : new config(),
                ckeditor: null
            }
        },
        mounted(){
            let vm = this
            vm.getProvinces()
            vm.getRatings()
            vm.getDepartments()
            vm.getCourses()
            CKEDITOR.replace('textarea-info').on('change',function () {
                vm.info.introduce = this.getData()
            })
        },
        methods:{
            submitInsert(){
                let vm =this
                vm.info.branch_code = vm.info.branch_code.code
                vm.info.course_code = vm.info.course_code.code
                vm.info.province_id = vm.info.province_id.id
                if(vm.info.rating_id != null && vm.info.rating_id != undefined)
                {
                    vm.info.rating_id = vm.info.rating_id.id
                }
                axios.post(vm.config.API_ADMIN_STUDENTS_RESOURCE,vm.info).then(data => {
                   vm.config.notifySuccess()
                    setTimeout(function () {
                        window.location.href = vm.config.WEB_ADMIN_STUDENTS
                    },2000)
                }).catch(err => {
                    console.dir(err)
                    if(err.response.status == 422)
                    {
                        let message = ''
                        message = err.response.data.message
                        message+= '<br>'
                        let errors = err.response.data.errors
                        let keys = Object.keys(errors)
                        keys.forEach(key => {
                            errors[key].forEach(item => {
                                message+=item+'<br>'
                            })
                        })
                        vm.config.notifyError(message)
                    }
                    else{
                        vm.config.notifyError()
                    }
                })
            },
            getProvinces(){
                let vm = this
                axios.get(vm.config.API_ADMIN_PROVINCES_RESOURCE+'?size=-1').then(data => {
                    vm.provinces = data.data.data
                }).catch(err => {

                })
            },
            getRatings(){
                let vm = this
                axios.get(vm.config.API_ADMIN_RATINGS_RESOURCE+'?size=-1').then(data => {
                    vm.ratings = data.data.data
                }).catch(err => {

                })
            },
            getCourses(){
                let vm = this
                axios.get(vm.config.API_ADMIN_COURSES_RESOURCE+'?size=-1').then(data => {
                    vm.courses = data.data.data
                }).catch(err => {

                })
            },
            getBranches(id){
                let vm = this
                axios.get(vm.config.API_ADMIN_DEPARTMENTS_RESOURCE+'/'+id).then(data => {
                    vm.branches = data.data.branches
                }).catch(err => {

                })
            },
            getDepartments(){
                let vm = this
                axios.get(vm.config.API_ADMIN_DEPARTMENTS_RESOURCE+'?size=-1').then(data => {
                    vm.departments = data.data.data
                }).catch(err => {

                })
            }

        },
    }
</script>
