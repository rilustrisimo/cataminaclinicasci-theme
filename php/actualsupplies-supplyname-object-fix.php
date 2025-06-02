<?php
/**
 * Simple Fix for ActualSupplies Supply Name Objects
 * 
 * If supply_name is not purely numeric, get the ID and update it.
 * Otherwise skip.
 */

require_once( __DIR__ . '/../../../../wp-load.php' );

// Ensure ACF is available
if (!function_exists('get_field') || !function_exists('update_field')) {
    die('ACF is not available. Please ensure Advanced Custom Fields Pro is installed and active.');
}

// Handle AJAX requests
if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if ($_POST['action'] === 'get_total_count') {
        $count_args = array(
            'post_type' => 'actualsupplies',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'post_status' => array('publish', 'draft', 'private'),
            'meta_query' => array(
                array(
                    'key' => 'related_release_id',
                    'value' => '',
                    'compare' => '!='
                )
            )
        );
        
        $count_query = new WP_Query($count_args);
        $total_posts = $count_query->found_posts;
        wp_reset_postdata();
        
        echo json_encode(['success' => true, 'total_posts' => $total_posts]);
        exit;
    }
    
    if ($_POST['action'] === 'process_batch') {
        $batch_size = intval($_POST['batch_size'] ?? 25);
        $offset = intval($_POST['offset'] ?? 0);
        
        $processed = 0;
        $updated = 0;
        $objects_found = 0;
        $logs = [];
        $errors = [];
        
        $args = array(
            'post_type' => 'actualsupplies',
            'posts_per_page' => $batch_size,
            'offset' => $offset,
            'post_status' => array('publish', 'draft', 'private'),
            'orderby' => 'ID',
            'order' => 'ASC',
            'meta_query' => array(
                array(
                    'key' => 'related_release_id',
                    'value' => '',
                    'compare' => '!='
                )
            )
        );
        
        $query = new WP_Query($args);
        
        if (!$query->have_posts()) {
            echo json_encode([
                'success' => true,
                'processed' => 0,
                'updated' => 0,
                'objects_found' => 0,
                'errors' => [],
                'logs' => [['message' => 'No more posts to process', 'type' => 'info']],
                'complete' => true
            ]);
            exit;
        }
        
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            $post_title = get_the_title();
            $processed++;
            
            try {
                // Get the related_release_id for this actualsupply
                $related_release_id = get_field('related_release_id', $post_id);
                
                // Skip if no related release ID
                if (empty($related_release_id)) {
                    $logs[] = ['message' => "Post #{$post_id} '{$post_title}' - No related_release_id, skipping", 'type' => 'info'];
                    continue;
                }
                
                // Get the release post to find the correct supply_name
                $release_post = get_post($related_release_id);
                if (!$release_post || $release_post->post_type !== 'releasesupplies') {
                    $logs[] = ['message' => "Post #{$post_id} '{$post_title}' - Invalid release ID {$related_release_id}", 'type' => 'warning'];
                    continue;
                }
                
                // Get the supply_name from the release post
                $release_supply_name = get_field('supply_name', $related_release_id);
                if (empty($release_supply_name)) {
                    $logs[] = ['message' => "Post #{$post_id} '{$post_title}' - No supply_name in release #{$related_release_id}", 'type' => 'warning'];
                    continue;
                }
                
                // Extract supply ID from release supply_name
                $supply_id = null;
                $object_info = '';
                
                if (is_object($release_supply_name) && isset($release_supply_name->ID)) {
                    // It's a WP_Post object (most common case)
                    $supply_id = $release_supply_name->ID;
                    $object_info = "From release #{$related_release_id}: WP_Post object (ID: {$supply_id}, Title: '{$release_supply_name->post_title}')";
                } elseif (is_numeric($release_supply_name)) {
                    // It's already a numeric ID
                    $supply_id = $release_supply_name;
                    $object_info = "From release #{$related_release_id}: Numeric ID: {$supply_id}";
                } else {
                    $logs[] = ['message' => "Post #{$post_id} '{$post_title}' - Unexpected supply_name format in release #{$related_release_id}: " . gettype($release_supply_name), 'type' => 'warning'];
                    continue;
                }
                
                if (!$supply_id) {
                    $logs[] = ['message' => "Post #{$post_id} '{$post_title}' - Could not extract supply ID from release #{$related_release_id}", 'type' => 'warning'];
                    continue;
                }
                
                $objects_found++;
                $logs[] = ['message' => "🔍 Post #{$post_id} - Found {$object_info}", 'type' => 'warning'];
                
                // Verify the supply post exists
                $supply_post = get_post($supply_id);
                if (!$supply_post || $supply_post->post_type !== 'supplies') {
                    $error = "Post #{$post_id} - Invalid supply ID {$supply_id}";
                    $errors[] = $error;
                    $logs[] = ['message' => "❌ " . $error, 'type' => 'error'];
                    continue;
                }
                
                // Update the field with just the ID
                $update_result = update_field('supply_name', $supply_id, $post_id);
                
                if ($update_result) {
                    $logs[] = ['message' => "✅ Post #{$post_id} - Updated supply_name to ID: {$supply_id}", 'type' => 'success'];
                    $updated++;
                    
                    // Update the post title to be more descriptive
                    $supply_title = get_the_title($supply_id);
                    $new_title = 'Actual Supply from Release - ' . $supply_title;
                    
                    wp_update_post(array(
                        'ID' => $post_id,
                        'post_title' => $new_title,
                    ));
                } else {
                    $error = "Post #{$post_id} - Failed to update supply_name field";
                    $errors[] = $error;
                    $logs[] = ['message' => "❌ " . $error, 'type' => 'error'];
                }
                
            } catch (Exception $e) {
                $error = "Post #{$post_id} - Exception: " . $e->getMessage();
                $errors[] = $error;
                $logs[] = ['message' => "❌ " . $error, 'type' => 'error'];
            }
        }
        
        wp_reset_postdata();
        
        echo json_encode([
            'success' => true,
            'processed' => $processed,
            'updated' => $updated,
            'objects_found' => $objects_found,
            'errors' => $errors,
            'logs' => $logs,
            'complete' => false
        ]);
        exit;
    }
    
    echo json_encode(['success' => false, 'message' => 'Unknown action']);
    exit;
}

// Show the HTML interface
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Fix ActualSupplies Supply Name Objects</title>
    <style>
        * { box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
            margin: 0; 
            padding: 20px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container { 
            max-width: 800px; 
            margin: 0 auto; 
            background: white; 
            padding: 30px; 
            border-radius: 12px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.15); 
        }
        h1 { 
            color: #2c3e50; 
            margin-bottom: 10px; 
            font-size: 28px;
            text-align: center;
        }
        .subtitle { 
            color: #7f8c8d; 
            text-align: center; 
            margin-bottom: 30px;
            font-size: 16px;
        }
        .progress-container { 
            margin: 25px 0; 
            position: relative;
        }
        .progress-bar { 
            width: 100%; 
            height: 35px; 
            background: #ecf0f1; 
            border-radius: 18px; 
            overflow: hidden; 
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
        }
        .progress-fill { 
            height: 100%; 
            background: linear-gradient(90deg, #3498db, #2ecc71); 
            width: 0%; 
            transition: width 0.5s ease; 
            border-radius: 18px;
        }
        .progress-text { 
            text-align: center; 
            margin-top: 12px; 
            font-weight: 600; 
            color: #2c3e50;
            font-size: 14px;
        }
        .log-container { 
            max-height: 400px; 
            overflow-y: auto; 
            background: #f8f9fa; 
            padding: 20px; 
            border-radius: 8px; 
            margin: 25px 0; 
            font-family: 'Monaco', 'Menlo', monospace; 
            font-size: 13px;
            border: 1px solid #e9ecef;
        }
        .log-entry {
            margin: 8px 0;
            padding: 6px 10px;
            border-radius: 4px;
            border-left: 3px solid #ddd;
        }
        .log-entry.info { background: #e8f4fd; border-left-color: #3498db; color: #2980b9; }
        .log-entry.success { background: #eafaf1; border-left-color: #27ae60; color: #27ae60; }
        .log-entry.error { background: #fdeaea; border-left-color: #e74c3c; color: #c0392b; }
        .log-entry.warning { background: #fef9e7; border-left-color: #f39c12; color: #e67e22; }
        .btn { 
            padding: 14px 28px; 
            background: linear-gradient(135deg, #3498db, #2980b9); 
            color: white; 
            border: none; 
            border-radius: 6px; 
            cursor: pointer; 
            font-size: 16px; 
            font-weight: 600;
            transition: all 0.3s ease;
            margin: 5px;
        }
        .btn:hover { 
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
        }
        .btn:disabled { 
            background: #bdc3c7; 
            cursor: not-allowed; 
            transform: none;
            box-shadow: none;
        }
        .stats { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); 
            gap: 20px; 
            margin: 25px 0; 
        }
        .stat-box { 
            padding: 20px; 
            background: linear-gradient(135deg, #f8f9fa, #e9ecef); 
            border-radius: 8px; 
            text-align: center;
            border: 1px solid #dee2e6;
        }
        .stat-number { 
            font-size: 32px; 
            font-weight: 700; 
            color: #2c3e50; 
            margin-bottom: 5px;
        }
        .stat-label { 
            font-size: 13px; 
            color: #7f8c8d; 
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        .button-container {
            text-align: center;
            margin: 25px 0;
        }
        .status { 
            padding: 15px 20px; 
            margin: 20px 0; 
            border-radius: 8px; 
            border-left: 4px solid;
        }
        .status.info { background: #e8f4fd; border-left-color: #3498db; color: #2980b9; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Simple Fix Supply Name Objects</h1>
        <p class="subtitle">If supply_name is not purely numeric, extract the ID and update it</p>
        
        <div id="initial-info" class="status info">
            <strong>Simple approach:</strong> This tool checks if supply_name is purely numeric. If not, it extracts the ID from whatever object/data is there and updates the field.
        </div>
        
        <div class="stats">
            <div class="stat-box">
                <div class="stat-number" id="total-posts">-</div>
                <div class="stat-label">Total Posts</div>
            </div>
            <div class="stat-box">
                <div class="stat-number" id="processed-posts">0</div>
                <div class="stat-label">Processed</div>
            </div>
            <div class="stat-box">
                <div class="stat-number" id="objects-found">0</div>
                <div class="stat-label">Objects Found</div>
            </div>
            <div class="stat-box">
                <div class="stat-number" id="posts-updated">0</div>
                <div class="stat-label">Posts Fixed</div>
            </div>
        </div>
        
        <div class="progress-container">
            <div class="progress-bar">
                <div class="progress-fill" id="progress-fill"></div>
            </div>
            <div class="progress-text" id="progress-text">Ready to start</div>
        </div>
        
        <div class="button-container">
            <button id="start-btn" class="btn" onclick="startProcessing()">Start Processing</button>
            <button id="stop-btn" class="btn" onclick="stopProcessing()" style="display:none; background:#dc3545;">Stop Processing</button>
        </div>
        
        <div class="log-container" id="log-container">
            <div style="color: #666; font-style: italic;">Logs will appear here when processing starts...</div>
        </div>
    </div>

    <script>
        let processing = false;
        let currentOffset = 0;
        let totalPosts = 0;
        let processedPosts = 0;
        let objectsFound = 0;
        let postsUpdated = 0;
        let batchSize = 25;
        
        function updateProgress() {
            const percentage = totalPosts > 0 ? Math.round((processedPosts / totalPosts) * 100) : 0;
            document.getElementById('progress-fill').style.width = percentage + '%';
            document.getElementById('progress-text').textContent = `${percentage}% Complete (${processedPosts}/${totalPosts})`;
            
            document.getElementById('processed-posts').textContent = processedPosts.toLocaleString();
            document.getElementById('objects-found').textContent = objectsFound.toLocaleString();
            document.getElementById('posts-updated').textContent = postsUpdated.toLocaleString();
            
            if (totalPosts > 0) {
                document.getElementById('total-posts').textContent = totalPosts.toLocaleString();
            }
        }
        
        function addLog(message, type = 'info') {
            const logContainer = document.getElementById('log-container');
            
            // Remove empty state if it exists
            const emptyState = logContainer.querySelector('div[style*="font-style: italic"]');
            if (emptyState) {
                emptyState.remove();
            }
            
            const timestamp = new Date().toLocaleTimeString();
            const logEntry = document.createElement('div');
            logEntry.className = `log-entry ${type}`;
            logEntry.innerHTML = `<span style="color: #666;">[${timestamp}]</span> ${message}`;
            
            logContainer.appendChild(logEntry);
            logContainer.scrollTop = logContainer.scrollHeight;
        }
        
        function setUIState(isProcessing) {
            document.getElementById('start-btn').style.display = isProcessing ? 'none' : 'inline-block';
            document.getElementById('stop-btn').style.display = isProcessing ? 'inline-block' : 'none';
            
            if (!isProcessing) {
                document.getElementById('start-btn').textContent = currentOffset > 0 ? 'Resume Processing' : 'Start Processing';
            }
        }
        
        async function startProcessing() {
            processing = true;
            setUIState(true);
            document.getElementById('initial-info').style.display = 'none';
            
            addLog('🚀 Starting simple processing...', 'info');
            
            try {
                // Get total count if we don't have it
                if (totalPosts === 0) {
                    addLog('📊 Getting total post count...', 'info');
                    const countResponse = await fetch(window.location.href, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'action=get_total_count'
                    });
                    
                    const countData = await countResponse.json();
                    if (!countData.success) {
                        throw new Error('Failed to get total count');
                    }
                    
                    totalPosts = countData.total_posts;
                    addLog(`📊 Found ${totalPosts.toLocaleString()} actualsupplies posts to check`, 'info');
                    updateProgress();
                }
                
                // Process in batches
                while (processing && currentOffset < totalPosts) {
                    addLog(`📦 Processing batch: ${currentOffset + 1}-${Math.min(currentOffset + batchSize, totalPosts)}`, 'info');
                    
                    const response = await fetch(window.location.href, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `action=process_batch&offset=${currentOffset}&batch_size=${batchSize}`
                    });
                    
                    const data = await response.json();
                    
                    if (!data.success) {
                        throw new Error(data.message || 'Batch processing failed');
                    }
                    
                    // Update counters
                    processedPosts += data.processed;
                    objectsFound += data.objects_found;
                    postsUpdated += data.updated;
                    
                    // Add logs for this batch
                    data.logs.forEach(log => {
                        addLog(log.message, log.type || 'info');
                    });
                    
                    updateProgress();
                    currentOffset += batchSize;
                    
                    // Check if we're done
                    if (data.complete) {
                        break;
                    }
                    
                    // Small delay to prevent overwhelming the server
                    await new Promise(resolve => setTimeout(resolve, 200));
                }
                
                if (processing) {
                    addLog('✅ Processing completed successfully!', 'success');
                    addLog(`📊 Final Summary: ${processedPosts.toLocaleString()} processed, ${objectsFound.toLocaleString()} objects found, ${postsUpdated.toLocaleString()} posts fixed`, 'success');
                    
                    // Reset for next run
                    currentOffset = 0;
                }
                
            } catch (error) {
                addLog(`❌ Error during processing: ${error.message}`, 'error');
                console.error('Processing error:', error);
            }
            
            processing = false;
            setUIState(false);
        }
        
        function stopProcessing() {
            processing = false;
            addLog('⏹️ Processing stopped by user', 'warning');
            setUIState(false);
        }
        
        // Initialize
        updateProgress();
    </script>
</body>
</html>
