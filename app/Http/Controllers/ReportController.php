<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Reports\Engine\ReportEngine;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ReportController extends Controller
{
    public function pdf(Request $request, string $code, ReportEngine $engine): Response
    {
        $user = $request->user();
        abort_if($user === null, 401);

        return $engine->generate($code, 'pdf', $user, $request->query());
    }

    public function docx(Request $request, string $code, ReportEngine $engine): Response
    {
        $user = $request->user();
        abort_if($user === null, 401);

        return $engine->generate($code, 'docx', $user, $request->query());
    }
}
