<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddViewDebugInfo
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!app()->environment('production') &&
            $response instanceof Response &&
            str_contains($response->headers->get('content-type') ?? '', 'text/html')
        ) {
            $content = $response->getContent();

            // Aggiungi uno script per mostrare il path nella console del browser
            $debugScript = sprintf('
                <script>
                    console.group("🔍 View Debug Info");
                    console.log(
                        "Current Route: %s",
                        "%s"
                    );
                    console.log(
                        "Current View: %s",
                        "%s"
                    );
                    console.groupEnd();
                </script>
            ',
                request()->route()?->getName() ?? request()->path(),
                request()->url(),
                view()->shared('__current_view_path', 'Unknown View'),
                request()->route()?->getActionName() ?? 'Unknown Action'
            );

            // Inserisci lo script prima della chiusura del body
            $content = preg_replace('/<\/body>/', $debugScript . '</body>', $content, 1);
            $response->setContent($content);
        }

        return $response;
    }
}
