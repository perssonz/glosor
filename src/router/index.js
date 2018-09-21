import Vue from 'vue'
import Router from 'vue-router'
import TheMain from '@/components/TheMain'
import TheAdd from '@/components/TheAdd'
import TheTrain from '@/components/TheTrain'

Vue.use(Router)

export default new Router({
        routes: [
                {
                        path: '/',
                        name: 'index',
                        component: TheMain
                },
                {
                        path: '/add',
                        name: 'add',
                        component: TheAdd
                },
                {
                        path: '/train',
                        name: 'train',
                        component: TheTrain,
                        props: (route) => ({ category: route.query.c })
                }
        ]
})
