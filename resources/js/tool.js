import Tool from './components/Tool'
import RouteViewerIcon from './components/icons/RouteViewer'

Nova.booting((app, store) => {
    Nova.inertia('RouteViewer', Tool)
    app.component('heroicons-outline-route-viewer', RouteViewerIcon)
})
