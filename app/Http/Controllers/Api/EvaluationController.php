<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\EvaluationTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EvaluationController extends Controller
{
    /**
     * Display a listing of evaluations.
     */
    public function index(Request $request)
    {
        $query = Evaluation::with(['template', 'evaluator']);

        // Filter by evaluable type
        if ($request->has('evaluable_type')) {
            $query->where('evaluable_type', $request->evaluable_type);
        }

        // Filter by evaluable id
        if ($request->has('evaluable_id')) {
            $query->where('evaluable_id', $request->evaluable_id);
        }

        // Filter by evaluator
        if ($request->has('evaluator_id')) {
            $query->where('evaluator_id', $request->evaluator_id);
        }

        // Filter by template
        if ($request->has('template_id')) {
            $query->where('template_id', $request->template_id);
        }

        $evaluations = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($evaluations);
    }

    /**
     * Store a newly created evaluation.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'evaluable_type' => 'required|string',
            'evaluable_id' => 'required|integer',
            'template_id' => 'required|exists:evaluation_templates,id',
            'total_score' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'criteria_scores' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $evaluation = Evaluation::create([
            'evaluable_type' => $request->evaluable_type,
            'evaluable_id' => $request->evaluable_id,
            'template_id' => $request->template_id,
            'evaluator_id' => auth()->id(),
            'total_score' => $request->total_score,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'message' => 'Evaluation created successfully',
            'data' => $evaluation->load(['template', 'evaluator'])
        ], 201);
    }

    /**
     * Display the specified evaluation.
     */
    public function show($id)
    {
        $evaluation = Evaluation::with(['template.criteria', 'evaluator', 'evaluable'])
            ->find($id);

        if (!$evaluation) {
            return response()->json([
                'message' => 'Evaluation not found'
            ], 404);
        }

        return response()->json($evaluation);
    }

    /**
     * Update the specified evaluation.
     */
    public function update(Request $request, $id)
    {
        $evaluation = Evaluation::find($id);

        if (!$evaluation) {
            return response()->json([
                'message' => 'Evaluation not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'total_score' => 'sometimes|required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $evaluation->update($request->only(['total_score', 'notes']));

        return response()->json([
            'message' => 'Evaluation updated successfully',
            'data' => $evaluation->load(['template', 'evaluator'])
        ]);
    }

    /**
     * Remove the specified evaluation.
     */
    public function destroy($id)
    {
        $evaluation = Evaluation::find($id);

        if (!$evaluation) {
            return response()->json([
                'message' => 'Evaluation not found'
            ], 404);
        }

        $evaluation->delete();

        return response()->json([
            'message' => 'Evaluation deleted successfully'
        ]);
    }

    /**
     * Get evaluation templates.
     */
    public function templates(Request $request)
    {
        $query = EvaluationTemplate::with('criteria');

        // Filter by evaluable type
        if ($request->has('evaluable_type')) {
            $query->where('evaluable_type', $request->evaluable_type);
        }

        // Only active templates
        if ($request->has('active_only') && $request->active_only) {
            $query->where('is_active', true);
        }

        $templates = $query->get();

        return response()->json($templates);
    }

    /**
     * Get evaluations for a specific entity.
     */
    public function forEntity($type, $id)
    {
        $evaluations = Evaluation::with(['template', 'evaluator'])
            ->where('evaluable_type', $type)
            ->where('evaluable_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($evaluations);
    }
}
