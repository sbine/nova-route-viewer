Nova.booting((Vue, router) => {
    router.addRoutes([
        {
            name: 'route-viewer',
            path: '/route-viewer',
            component: require('./components/Tool'),
        },
    ])
})
