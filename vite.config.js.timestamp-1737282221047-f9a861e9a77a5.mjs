// vite.config.js
import { defineConfig } from "file:///home/fabio/EGI/node_modules/vite/dist/node/index.js";
import laravel from "file:///home/fabio/EGI/node_modules/laravel-vite-plugin/dist/index.js";
var vite_config_default = defineConfig({
  plugins: [
    laravel({
      input: ["resources/css/app.css", "resources/js/app.js"],
      refresh: [
        "resources/**",
        "routes/**",
        "app/**"
      ]
    })
  ],
  server: {
    hmr: {
      overlay: true
    },
    watch: {
      usePolling: true,
      interval: 1e3
    }
  },
  // Rimosso il blocco css che causava il problema
  build: {
    cssCodeSplit: true,
    chunkSizeWarningLimit: 1e3
  },
  optimizeDeps: {
    include: ["tailwindcss", "daisyui"]
  }
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCIvaG9tZS9mYWJpby9FR0lcIjtjb25zdCBfX3ZpdGVfaW5qZWN0ZWRfb3JpZ2luYWxfZmlsZW5hbWUgPSBcIi9ob21lL2ZhYmlvL0VHSS92aXRlLmNvbmZpZy5qc1wiO2NvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9pbXBvcnRfbWV0YV91cmwgPSBcImZpbGU6Ly8vaG9tZS9mYWJpby9FR0kvdml0ZS5jb25maWcuanNcIjtpbXBvcnQgeyBkZWZpbmVDb25maWcgfSBmcm9tICd2aXRlJztcbmltcG9ydCBsYXJhdmVsIGZyb20gJ2xhcmF2ZWwtdml0ZS1wbHVnaW4nO1xuXG5leHBvcnQgZGVmYXVsdCBkZWZpbmVDb25maWcoe1xuICAgIHBsdWdpbnM6IFtcbiAgICAgICAgbGFyYXZlbCh7XG4gICAgICAgICAgICBpbnB1dDogWydyZXNvdXJjZXMvY3NzL2FwcC5jc3MnLCAncmVzb3VyY2VzL2pzL2FwcC5qcyddLFxuICAgICAgICAgICAgcmVmcmVzaDogW1xuICAgICAgICAgICAgICAgICdyZXNvdXJjZXMvKionLFxuICAgICAgICAgICAgICAgICdyb3V0ZXMvKionLFxuICAgICAgICAgICAgICAgICdhcHAvKionLFxuICAgICAgICAgICAgXSxcbiAgICAgICAgfSksXG4gICAgXSxcbiAgICBzZXJ2ZXI6IHtcbiAgICAgICAgaG1yOiB7XG4gICAgICAgICAgICBvdmVybGF5OiB0cnVlLFxuICAgICAgICB9LFxuICAgICAgICB3YXRjaDoge1xuICAgICAgICAgICAgdXNlUG9sbGluZzogdHJ1ZSxcbiAgICAgICAgICAgIGludGVydmFsOiAxMDAwLFxuICAgICAgICB9XG4gICAgfSxcbiAgICAvLyBSaW1vc3NvIGlsIGJsb2NjbyBjc3MgY2hlIGNhdXNhdmEgaWwgcHJvYmxlbWFcbiAgICBidWlsZDoge1xuICAgICAgICBjc3NDb2RlU3BsaXQ6IHRydWUsXG4gICAgICAgIGNodW5rU2l6ZVdhcm5pbmdMaW1pdDogMTAwMCxcbiAgICB9LFxuICAgIG9wdGltaXplRGVwczoge1xuICAgICAgICBpbmNsdWRlOiBbJ3RhaWx3aW5kY3NzJywgJ2RhaXN5dWknXSxcbiAgICB9XG59KTtcbiJdLAogICJtYXBwaW5ncyI6ICI7QUFBK04sU0FBUyxvQkFBb0I7QUFDNVAsT0FBTyxhQUFhO0FBRXBCLElBQU8sc0JBQVEsYUFBYTtBQUFBLEVBQ3hCLFNBQVM7QUFBQSxJQUNMLFFBQVE7QUFBQSxNQUNKLE9BQU8sQ0FBQyx5QkFBeUIscUJBQXFCO0FBQUEsTUFDdEQsU0FBUztBQUFBLFFBQ0w7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLE1BQ0o7QUFBQSxJQUNKLENBQUM7QUFBQSxFQUNMO0FBQUEsRUFDQSxRQUFRO0FBQUEsSUFDSixLQUFLO0FBQUEsTUFDRCxTQUFTO0FBQUEsSUFDYjtBQUFBLElBQ0EsT0FBTztBQUFBLE1BQ0gsWUFBWTtBQUFBLE1BQ1osVUFBVTtBQUFBLElBQ2Q7QUFBQSxFQUNKO0FBQUE7QUFBQSxFQUVBLE9BQU87QUFBQSxJQUNILGNBQWM7QUFBQSxJQUNkLHVCQUF1QjtBQUFBLEVBQzNCO0FBQUEsRUFDQSxjQUFjO0FBQUEsSUFDVixTQUFTLENBQUMsZUFBZSxTQUFTO0FBQUEsRUFDdEM7QUFDSixDQUFDOyIsCiAgIm5hbWVzIjogW10KfQo=
