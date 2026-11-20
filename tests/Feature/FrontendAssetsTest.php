<?php

namespace Tests\Feature;

use Tests\TestCase;

class FrontendAssetsTest extends TestCase
{
    public function test_vite_manifest_contains_real_css_js_and_generated_tailwind_utilities(): void
    {
        $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true, flags: JSON_THROW_ON_ERROR);
        foreach (['resources/css/app.css', 'resources/css/login.css', 'resources/js/app.js'] as $entry) {
            $this->assertTrue($manifest[$entry]['isEntry']);
            $this->assertFileExists(public_path('build/'.$manifest[$entry]['file']));
        }
        $css = file_get_contents(public_path('build/'.$manifest['resources/css/login.css']['file']));
        $this->assertStringContainsString('.tw\\:flex{display:flex}', $css);
        $this->assertStringContainsString('.tw\\:justify-center{justify-content:center}', $css);
        $this->assertStringNotContainsString('preflight.css', file_get_contents(resource_path('css/tailwind.css')));
        $js = file_get_contents(public_path('build/'.$manifest['resources/js/app.js']['file']));
        $this->assertStringContainsString('menuButton', $js);
        $this->assertStringContainsString('sidebarOverlay', $js);
    }

    public function test_login_uses_its_isolated_compiled_styles_and_one_global_module(): void
    {
        $response = $this->get('/')->assertOk()->assertSee('login-footer tw:flex tw:justify-center');
        $html = $response->getContent();
        $this->assertMatchesRegularExpression('~/build/assets/login-[^" ]+\.css~', $html);
        $this->assertDoesNotMatchRegularExpression('~/build/assets/app-[^" ]+\.css~', $html);
        $this->assertSame(1, preg_match_all('~<script[^>]+type="module"~', $html));
        $response->assertDontSee('/css/login.css')->assertDontSee('/js/navigation.js');
        foreach (['app', 'student', 'teacher', 'company'] as $layout) {
            $source = file_get_contents(resource_path('views/layouts/'.$layout.'.blade.php'));
            $this->assertSame(1, substr_count($source, "@vite(['resources/css/app.css', 'resources/js/app.js'])"));
            $this->assertStringNotContainsString("asset('css/", $source);
            $this->assertStringNotContainsString("asset('js/navigation.js')", $source);
        }
    }
}
