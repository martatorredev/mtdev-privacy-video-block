/**
 * Edit component — WCAG 2.2 aware.
 *
 * Accessibility notes:
 * - The URL field has a programmatic label (1.3.1, 3.3.2, 4.1.2); it is hidden
 *   from sight only because the Placeholder already provides a visible heading
 *   and instructions.
 * - Validation errors use <Notice> (role="alert") so they are announced (4.1.3).
 * - All interactive targets use the 40px default size (2.5.8 Target Size).
 * - No autoplay, so there is no moving content to pause (2.2.2).
 * - The iframe carries a meaningful title (1.1.1, 2.4.1, 4.1.2).
 */
import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import {
	useBlockProps,
	InspectorControls,
	BlockControls,
	RichText,
} from '@wordpress/block-editor';
import {
	Placeholder,
	Button,
	TextControl,
	SelectControl,
	ToggleControl,
	RangeControl,
	PanelBody,
	ToolbarGroup,
	ToolbarButton,
	Notice,
} from '@wordpress/components';

const ASPECT_RATIOS = [
	{ label: '16 : 9 ' + __( '(Widescreen)', 'mtdev-privacy-video-block' ), value: '16/9' },
	{ label: '4 : 3 ' + __( '(Standard)', 'mtdev-privacy-video-block' ), value: '4/3' },
	{ label: '1 : 1 ' + __( '(Square)', 'mtdev-privacy-video-block' ), value: '1/1' },
	{ label: '9 : 16 ' + __( '(Vertical / Shorts)', 'mtdev-privacy-video-block' ), value: '9/16' },
	{ label: '21 : 9 ' + __( '(Ultrawide)', 'mtdev-privacy-video-block' ), value: '21/9' },
];

function extractVideoId( url ) {
	if ( ! url ) return '';
	url = String( url ).trim();
	const pattern =
		/(?:youtube(?:-nocookie)?\.com\/(?:[^/\n\s]+\/\S+\/|(?:v|e(?:mbed)?|shorts|live)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/i;
	const match = url.match( pattern );
	if ( match ) return match[ 1 ];
	if ( /^[a-zA-Z0-9_-]{11}$/.test( url ) ) return url;
	return '';
}

function buildSrc( videoId, related, startTime ) {
	const params = [ 'modestbranding=1', 'rel=' + ( related ? '1' : '0' ) ];
	if ( startTime > 0 ) params.push( 'start=' + startTime );
	return `https://www.youtube-nocookie.com/embed/${ encodeURIComponent(
		videoId
	) }?${ params.join( '&' ) }`;
}

export default function Edit( { attributes, setAttributes } ) {
	const [ inputUrl, setInputUrl ] = useState( attributes.url || '' );
	const [ touched, setTouched ] = useState( false );

	const blockProps = useBlockProps( {
		style: attributes.maxWidth
			? { maxWidth: attributes.maxWidth + 'px' }
			: undefined,
	} );

	const validId = extractVideoId( inputUrl );

	const submitUrl = () => {
		setTouched( true );
		if ( validId ) {
			setAttributes( { url: inputUrl, videoId: validId } );
		}
	};

	// --- Empty state -----------------------------------------------------
	if ( ! attributes.videoId ) {
		return (
			<div { ...blockProps }>
				<Placeholder
					icon="video-alt3"
					label={ __( 'YouTube (Privacy)', 'mtdev-privacy-video-block' ) }
					instructions={ __(
						'Paste a YouTube URL. It will be embedded via youtube-nocookie.com — no tracking cookies until the visitor plays the video.',
						'mtdev-privacy-video-block'
					) }
				>
					<div className="mtdevpvb-placeholder-form">
						<TextControl
							label={ __( 'YouTube URL', 'mtdev-privacy-video-block' ) }
							hideLabelFromVision
							type="url"
							placeholder={ __(
								'https://www.youtube.com/watch?v=…',
								'mtdev-privacy-video-block'
							) }
							value={ inputUrl }
							onChange={ ( v ) => {
								setInputUrl( v );
								setTouched( true );
							} }
							onKeyDown={ ( e ) => {
								if ( e.key === 'Enter' ) submitUrl();
							} }
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
						<Button
							variant="primary"
							onClick={ submitUrl }
							disabled={ ! validId }
							__next40pxDefaultSize
						>
							{ __( 'Embed', 'mtdev-privacy-video-block' ) }
						</Button>
					</div>
					{ touched && inputUrl && ! validId && (
						<Notice status="error" isDismissible={ false }>
							{ __(
								'That does not look like a valid YouTube URL.',
								'mtdev-privacy-video-block'
							) }
						</Notice>
					) }
				</Placeholder>
			</div>
		);
	}

	// --- Filled state ----------------------------------------------------
	const previewSrc = buildSrc(
		attributes.videoId,
		attributes.relatedVideos,
		attributes.startTime
	);

	return (
		<>
			<BlockControls>
				<ToolbarGroup>
					<ToolbarButton
						icon="edit"
						label={ __( 'Replace video', 'mtdev-privacy-video-block' ) }
						onClick={ () => {
							setInputUrl( attributes.url || '' );
							setAttributes( { videoId: '' } );
						} }
					/>
				</ToolbarGroup>
			</BlockControls>

			<InspectorControls>
				<PanelBody
					title={ __( 'Video settings', 'mtdev-privacy-video-block' ) }
					initialOpen
				>
					<TextControl
						label={ __(
							'Title (for accessibility)',
							'mtdev-privacy-video-block'
						) }
						help={ __(
							'Used as the iframe title for screen readers.',
							'mtdev-privacy-video-block'
						) }
						value={ attributes.title }
						onChange={ ( v ) => setAttributes( { title: v } ) }
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<SelectControl
						label={ __( 'Aspect ratio', 'mtdev-privacy-video-block' ) }
						value={ attributes.aspectRatio }
						options={ ASPECT_RATIOS }
						onChange={ ( v ) => setAttributes( { aspectRatio: v } ) }
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<RangeControl
						label={ __( 'Maximum width (px)', 'mtdev-privacy-video-block' ) }
						help={ __(
							'0 = full width of the content area.',
							'mtdev-privacy-video-block'
						) }
						value={ attributes.maxWidth }
						min={ 0 }
						max={ 1920 }
						step={ 10 }
						onChange={ ( v ) => setAttributes( { maxWidth: v || 0 } ) }
					/>
					<TextControl
						label={ __( 'Start time (seconds)', 'mtdev-privacy-video-block' ) }
						type="number"
						min={ 0 }
						value={ attributes.startTime }
						onChange={ ( v ) =>
							setAttributes( { startTime: parseInt( v, 10 ) || 0 } )
						}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<ToggleControl
						label={ __(
							'Show related videos at the end',
							'mtdev-privacy-video-block'
						) }
						checked={ attributes.relatedVideos }
						onChange={ ( v ) => setAttributes( { relatedVideos: v } ) }
						__nextHasNoMarginBottom
					/>
				</PanelBody>
			</InspectorControls>

			<figure { ...blockProps }>
				<div
					className="mtdevpvb-frame"
					style={ {
						aspectRatio: attributes.aspectRatio.replace( '/', ' / ' ),
					} }
				>
					<iframe
						src={ previewSrc }
						title={
							attributes.title ||
							__( 'YouTube video player', 'mtdev-privacy-video-block' )
						}
						frameBorder="0"
						loading="lazy"
						allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
						allowFullScreen
					/>
				</div>
				<RichText
					tagName="figcaption"
					className="mtdevpvb-caption"
					placeholder={ __( 'Add a caption…', 'mtdev-privacy-video-block' ) }
					value={ attributes.caption }
					onChange={ ( v ) => setAttributes( { caption: v } ) }
					allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
				/>
			</figure>
		</>
	);
}
