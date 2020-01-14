<?php

namespace App\Services\ProductHunt\Models;

use Illuminate\Database\Eloquent\Model;

class EntityProduct extends Model
{    
    /** @var array */
    protected $fillable = ['votes', 'name', 'featured_at', 'updated_at'];

    /** @var string */
    protected $homepageRequestBody = '{ "operationName": "HomePage", "variables": { "cursor": "", "featured": true, "includePromotedPost": false, "page": "home", "includeLayout": false }, "query": "query HomePage( $cursor: String $featured: Boolean! $includePromotedPost: Boolean! ) { sections(first: 1, after: $cursor, featured: $featured) { edges { node { id date cutoff_index cards(first: 1, after: $cursor) { edges { node { ...FeedCards __typename } __typename } __typename } posts { edges { node { ...PostItemList ...PostCache __typename } __typename } __typename } __typename } __typename } pageInfo { endCursor hasNextPage __typename } __typename } promoted_post @include(if: $includePromotedPost) { id ...PromotedPostItem __typename } daily_newsletter { id subject __typename } viewer { id email has_newsletter_subscription __typename } } fragment FeedCards on Card { ...FeedUpcomingPagesCard ...NewPostsCard ...BestProductsFromLastWeekCard ...MakersDiscussionCardFragment __typename } fragment NewPostsCard on NewPostsCard { is_dismissed kind posts { ...PostItemList __typename } __typename } fragment PostItemList on Post { id ...PostItem __typename } fragment PostItem on Post { id _id comments_count name shortened_url slug tagline updated_at ...CollectButton ...PostThumbnail ...PostVoteButton ...TopicFollowButtonList __typename } fragment CollectButton on Post { id name isCollected __typename } fragment PostThumbnail on Post { id name thumbnail { id media_type ...MediaThumbnail __typename } ...PostStatusIcons __typename } fragment MediaThumbnail on Media { id image_uuid __typename } fragment PostStatusIcons on Post { name product_state __typename } fragment PostVoteButton on Post { _id id featured_at updated_at disabled_when_scheduled has_voted ... on Votable { id votes_count __typename } __typename } fragment TopicFollowButtonList on Topicable { id topics { edges { node { id ...TopicFollowButton __typename } __typename } __typename } __typename } fragment TopicFollowButton on Topic { id slug name isFollowed ...TopicImage __typename } fragment TopicImage on Topic { name image_uuid __typename } fragment BestProductsFromLastWeekCard on BestProductsFromLastWeekCard { posts { ...PostItemList ...PostCache __typename } __typename } fragment PostCache on Post { _id can_manage id name tagline trashed_at ...PostStatusIcons ...PostThumbnail ...PostVoteButton ...TopicFollowButtonList __typename } fragment FeedUpcomingPagesCard on UpcomingPagesCard { is_dismissed upcoming_pages { ...UpcomingPageItem __typename } __typename } fragment UpcomingPageItem on UpcomingPage { id name tagline slug background_image_uuid thumbnail_uuid logo_uuid subscriber_count popular_subscribers { id twitter_username ...UserSpotlight __typename } ...UpcomingPageSubscribeButton __typename } fragment UserSpotlight on User { _id id headline name username ...UserImageLink __typename } fragment UserImageLink on User { id _id name username ...UserImage __typename } fragment UserImage on User { id post_upvote_streak name __typename } fragment UpcomingPageSubscribeButton on UpcomingPage { id is_subscribed __typename } fragment MakersDiscussionCardFragment on MakersDiscussionCard { isDismissed discussion { _id id ...DiscussionThreadListItem __typename } __typename } fragment DiscussionThreadListItem on DiscussionThread { _id id title description descriptionHtml slug commentsCount can_comment: canComment discussionPath canEdit votesCount hasVoted createdAt poll { ...PollFragment __typename } user { id name username headline __typename } __typename } fragment PollFragment on Poll { id answersCount hasAnswered options { id text imageUuid answersCount answersPercent hasAnswered __typename } __typename } fragment PromotedPostItem on PromotedPost { id deal post { id ...PostItem __typename } ...ViewableImpressionSubject __typename } fragment ViewableImpressionSubject on Node { id __typename }"}';

    public function getHomepageRequestBody(): string
    {
        return $this->homepageRequestBody;
    }

    public function entity()
    {
        return $this->morphOne(\App\Services\Scraper\Models\Entity::class, 'entityable');
    }
}
