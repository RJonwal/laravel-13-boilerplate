<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>

	<!-- FONT FAMILY -->
	<link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700;800&family=Nunito+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @yield('styles')
</head>
<body style="margin: 0;background: linear-gradient(349deg, rgba(254, 249, 225, 0.5) 0%, rgba(255, 255, 255, 0) 40%, rgba(1, 151, 205, 0.4) 100%);padding: 20px;background-repeat: no-repeat;background-size: cover;">
	<div class="mail-template" style="max-width: 100%; margin: 0 auto;">
		<table cellpadding="0" cellspacing="0" width="100%" style="max-width:600px;margin:0 auto;max-width:600px;margin:0 auto;border: 1px solid #00509d;border-radius: 12px;background: #fff;">
			<thead>
				<tr style="width:100%;">
					<th style="text-align: center;padding: 40px 40px 26px;">
                        <img src="{{ getSetting('site_logo') ? getSetting('site_logo') : asset(config('constant.default.logo')) }}" alt="" title="" style="max-width: 150px;max-height: 150px;" />
                        <!-- {{ getSetting('site_title') ? getSetting('site_title') : config('app.name') }} -->
                    </th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="padding: 0 40px 40px;">
                        @yield('email-content') 
					</td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td>
						<p class="copyright" style="box-sizing:border-box;margin-top:0;margin:0;background-color: #00509d;padding:20px;text-align:center;font-size: 14px;line-height: 18px;font-weight: 400;font-family:'Nunito Sans',sans-serif;color: #fff;border-radius: 0 0 12px 12px;">© {{ date('Y') }} All Copyrights Reserved By {{ getSetting('site_title') ? getSetting('site_title') : config('app.name') }}</p>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
	
</body>
</html>